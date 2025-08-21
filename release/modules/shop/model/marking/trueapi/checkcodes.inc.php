<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Marking\TrueApi;

use Main\Model\Requester\ExternalRequest;
use Main\Model\Requester\ExternalResponse;
use RS\Exception;
use RS\File\Tools;
use RS\Helper\PersistentStateFile;
use RS\Site\Manager;
use Shop\Config\File;
use Shop\Model\Log\LogTrueApi;
use Shop\Model\Orm\OrderItemUIT;

/**
 * Класс предоставляет методы для предварительной валидации кодов маркировки при их добавлении к заказу.
 */
class CheckCodes
{
    const TEST_BASE_HOST = 'https://markirovka.sandbox.crptech.ru';
    const BASE_HOST = 'https://cdn.crpt.ru';

    const URL_CDN_INFO = '/api/v4/true-api/cdn/info';
    const URL_CDN_HEALTH_CHECK = '/api/v4/true-api/cdn/health/check';
    const URL_CODES_CHECK = '/api/v4/true-api/codes/check';

    const HOST_BAN_SECONDS = 900; //Блокируем на 15 минут недоступный Хост
    const REQUEST_TIMEOUT = 5;

    protected $site_id;
    protected string $host_cache_folder;
    protected string $host_cache_filepath;
    protected string $base_host;
    protected File $config;
    protected PersistentStateFile $host_cache;

    /**
     * Конструктор класса
     */
    function __construct($site_id = null)
    {
        $this->site_id = $site_id ?? Manager::getSiteId();
        $this->config = File::config($site_id);

        $this->base_host = ($this->config['true_mark_test_mode'] ? self::TEST_BASE_HOST : self::BASE_HOST);
        $this->host_cache_folder = \Setup::$PATH.\Setup::$STORAGE_DIR.'/trueapi';
        $this->host_cache_filepath = $this->host_cache_folder.'/hosts'
            .($this->config['true_mark_test_mode'] ? '-test' : '').'.cache';
        Tools::makePrivateDir($this->host_cache_folder);

        //Будем сохранять список хостов "Честного знака" в кэш-файле
        $this->host_cache = new PersistentStateFile($this->host_cache_filepath);
    }

    /**
     * Очизает список хостов
     *
     * @return array
     */
    public function cleanHosts()
    {
        $this->host_cache->clean();
    }

    /**
     * Производит поиск наиболее быстрого подходящего хоста для работы
     *
     * @param bool $force Если false, то будет возвращен список хостов из кэша
     * Если true, то произойдет новый поиск подходящего узла и его возвращение
     *
     * @return array
     */
    public function getHosts($force = false)
    {
        $hosts = $this->host_cache->get('hosts', []);

        if ($force || !$hosts) {
            $hosts = [];
            $url = $this->base_host.self::URL_CDN_INFO;

            //Получаем список хостов
            $request = new ExternalRequest('true-api', $url);
            $request->setMethod(ExternalRequest::METHOD_GET);
            $request->setEnableCache(false);
            $request->setTimeout(self::REQUEST_TIMEOUT);
            $request->setContentType(ExternalRequest::CONTENT_TYPE_JSON);
            $request->setHeaders([
                'X-Api-Key' => $this->getToken()
            ]);
            $request->setLog(LogTrueApi::getInstance(), LogTrueApi::LEVEL_CDN_INFO);
            $response = $request->executeRequest();
            if ($response->getStatus() == 200) {
                $data = $response->getResponseJson();

                //Проверяем доступность
                foreach ($data['hosts'] as $host) {
                    $request->setUrl($host['host'] . self::URL_CDN_HEALTH_CHECK);
                    $start = microtime(true);
                    $health_check_response = $request->executeRequest();
                    if ($health_check_response->getStatus() == 200) {
                        $health_check_response = $health_check_response->getResponseJson();
                        if ($health_check_response['code'] == 0) {
                            $hosts[] = [
                                'host' => $host['host'],
                                'latency' => microtime(true) - $start,
                                'avgTimeMs' => $health_check_response['avgTimeMs'],
                                'banExpire' => 0 //0 - не заблокирован
                            ];
                        }
                    }
                }

                //Сортируем по времени ответа
                usort($hosts, function ($a, $b) {
                    return $a['latency'] <=> $b['latency'];
                });

                $this->host_cache->set('hosts', $hosts);
            }
        }

        return $hosts;
    }

    /**
     * Возвращает токен для работы с сервисом "Честный знак"
     *
     * @return string
     */
    protected function getToken()
    {
        return $this->config['true_mark_token']
            ?: throw new Exception(t('Не задан токен для "ЧестногоЗнака" в настройках модуля Магазин'));
    }

    /**
     * Возвращает результат проверки одной маркировки
     *
     * @param OrderItemUIT $uit Объект одной маркировки
     * @return UitCheckResult
     */
    public function checkUit($uit)
    {
        $results = $this->checkUits([$uit]);
        return reset($results);
    }

    /**
     * Возвращает результат проверки списка маркировок
     *
     * @param OrderItemUIT[] $uits
     * @return UitCheckResult[]
     */
    public function checkUits(array $uits)
    {
        $request_data = [];
        foreach($uits as $uit) {
            $request_data['codes'][] = $uit->asStringWithGS();
        }

        $result = [];
        try {
            $response = $this->retryRequest($request_data);

            //Записываем результат проверки
            foreach($uits as $uit) {
                if ($response['code'] == 0) { //OK
                    foreach ($response['codes'] as $check_result) {
                        if ($uit->asStringWithGS() == $check_result['cis']) {
                            $result[$uit['id']] = new UitCheckResult($uit, [
                                'code' => $check_result,
                                'reqId' => $response['reqId'],
                                'reqTimestamp' => $response['reqTimestamp'],
                            ]);
                        }
                    }

                    if (!isset($result[$uit['id']])) {
                        $result[$uit['id']] = new UitCheckResult($uit, [], t('Проверка не выполнена. Не удалось получить результат проверки для маркировки'));
                    }
                } else {
                    throw new Exception(t('Проверка не выполнена. Код ошибки: %0', [$response['code'].':'.$response['description']]));
                }
            }
        } catch (Exception $e) {
            foreach($uits as $uit) {
                //Запрос не выполнен, создаем результат с ошибкой
                $result[$uit['id']] = new UitCheckResult($uit, [], $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Выполняет запрос к серверам ЧестногоЗнака по списку
     *
     * @param array $data Данные для запроса на проверку кодов
     * @return array
     */
    protected function retryRequest($data)
    {
        $token = $this->getToken();
        $hosts = $this->getHosts();
        if (!$hosts) {
            throw new Exception(t('Проверка не выполнена. Не удалось получить список хостов ЧестногоЗнака'));
        }

        foreach($hosts as $host_data) {
            //Если у хоста есть пометка о недоступности до определенного времени, то пропускаем его
            if ($host_data['banExpire'] > time()) continue;

            $response = $this->doRequest($host_data['host'].self::URL_CODES_CHECK, $data, $token);
            $status_code = $response->getStatus();
            $json = $response->getResponseJson();
            if ($status_code == 200) {
                return $json;
            }

            if ($status_code == 401) {
                throw new Exception(t('Ошибка авторизации в Честном знаке. Проверьте корректность токена'));
            }

            if ($json && $json['code'] == 5000) {
                throw new Exception(t('Проверка не выполнена. Невозможно получить ответ о статусе кода от системы страны-эмитента, повторите попытку позже.'));
            }

            if ($status_code > 401 && $status_code <= 599) {
                $host_data['banExpire'] = time() + self::HOST_BAN_SECONDS;
                $this->host_cache->set('hosts', $hosts);
            }
        }

        //Если забанены все хосты, то удаляем их из кэша, чтобы в следующий раз они были получены заново
        $banned_all = array_filter($hosts, function($host) {
            return $host['banExpire'] > time();
        });

        if ($banned_all) {
            $this->host_cache->set('hosts', []);
        }

        throw new Exception(t('Проверка не выполнена. Все хосты ЧестногоЗнака недоступны'));
    }

    /**
     * Выполняет один запрос к серверу ЧестногоЗнака
     *
     * @param string $url
     * @param array $params
     * @param string $token
     *
     * @return ExternalResponse
     */
    protected function doRequest($url, $params, $token)
    {
        $request = new ExternalRequest('true-api', $url);
        $request->setMethod(ExternalRequest::METHOD_POST);
        $request->setTimeout(self::REQUEST_TIMEOUT);
        $request->setEnableCache(false);
        $request->setContentType(ExternalRequest::CONTENT_TYPE_JSON);
        $request->setParams($params);
        $request->setHeaders([
            'X-Api-Key' => $token
        ]);
        $request->setLog(LogTrueApi::getInstance(), LogTrueApi::LEVEL_CHECK_CODES);
        return $request->executeRequest();
    }

    /**
     * Возвращает true, если в настройках включена проверка кодов маркировки
     *
     * @param integer $site_id
     * @return bool
     */
    public static function isCheckCodesEnabled($site_id = null)
    {
        return File::config($site_id)->true_mark_check_codes;
    }

    /**
     * Возвращает true, если удалось авторизовать по указанному токену
     *
     * @return bool
     */
    public function checkAuthorization()
    {
        $url = $this->base_host.self::URL_CDN_INFO;

        $request = new ExternalRequest('true-api', $url);
        $request->setMethod(ExternalRequest::METHOD_GET);
        $request->setEnableCache(false);
        $request->setTimeout(self::REQUEST_TIMEOUT);
        $request->setContentType(ExternalRequest::CONTENT_TYPE_JSON);
        $request->setHeaders([
            'X-Api-Key' => $this->getToken()
        ]);
        $request->setLog(LogTrueApi::getInstance(), LogTrueApi::LEVEL_CDN_INFO);
        $response = $request->executeRequest();
        $json = $response->getResponseJson();
        if ($response->getStatus() == 200) {
            return true;
        }

        if ($json && $json['description']) {
            throw new Exception($json['description']);
        }

        throw new Exception(t('Не удалось авторизоваться (Статус ответа: %0).', [$response->getStatus()]));
    }
}