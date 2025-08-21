<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\ServiceType;

use Ai\Config\File;
use Ai\Model\Guzzle\StreamFinishCallback;
use Ai\Model\Log\AiLog;
use Ai\Model\Orm\Service;
use Ai\Model\Orm\Statistic;
use Ai\Model\ServiceModelApi;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use Traversable;
use RS\Orm\Type;

/**
 * Абстрактный базовый класс провайдера GPT-сервиса ReadyScript.
 *
 * Только класс типа сервиса (наследник этого класса) должен взаимодействовать с низкоуровневыми сторонними библиотеками,
 * предоставляющими доступ к API сервиса. Все остальные классы ReadyScript должны взаимодействовать с GPT только через
 * наследников этого базового класса.
 */
abstract class AbstractServiceType
{
    protected Service $service;
    protected File $config;
    protected array $statistic_params = [];

    /**
     * Конструктор
     *
     * @param Service $service ORM-объект настроек сервиса
     */
    function __construct(Service $service)
    {
        $this->service = $service;
        $this->config = File::config();
    }

    /**
     * Возвращает идентификатор сервиса
     *
     * @return string
     */
    abstract public static function getId();


    /**
     * Возвращает название сервиса
     *
     * @return string
     */
    abstract public static function getTitle();


    /**
     * Создает стрим для возвращения ответа для одиночной фразы.
     *
     * @param array $params
     * @return Traversable Должен вернуть генератор
     */
    abstract public function createChatStream(array $params):Traversable;

    /**
     * Возвращает список моделей, которые поддерживает данный сервис
     * В ключе идентификатор модели, в значении - название
     *
     * @return array
     */
    abstract public function getModelList();

    /**
     * Возвращает описание сервиса
     *
     * @return string
     */
    abstract public function getDescriptionHtml();


    /**
     * Возвращает объект, для отображения формы для настроек сервиса
     *
     * @return FormObject
     */
    public function getSettingsFormObject()
    {
        $form_object = new FormObject((new PropertyIterator([
            'api_url' => (new Type\Varchar())
                ->setDescription(t('API Base URL'))
                ->setHint(t('Без https://. Если оставите поле пустым, будет использоваться стандартный базовый URL сервиса.')),
            'api_key' => (new Type\Varchar())
                ->setDescription(t('API ключ'))
                ->setHint(t('Получите API-ключ в сервисе')),
            'model' => (new Type\Varchar())
                ->setDescription(t('Модель'))
                ->setHint(t('Укажите корректные доступы к сервису и нажмите кнопку `Обновить список`. Затем выберите ИИ-модель, с которой будет связана данная учетная запись.'))
                ->setChecker('ChkEmpty', t('Выберите модель'))
                ->setAttr([
                    'size' => 1
                ])
                ->setTemplate('%ai%/admin/service/model.tpl'),
        ]))->arrayWrap('settings'));

        $models = ServiceModelApi::staticSelectListByType($this->service['type']);
        $form_object['__model']->setListFromArray($models ? ['' => t('- Не выбрано -')] + $models : ['' => t('- Обновите список -')]);

        return $form_object;
    }

    /**
     * Возвращает HTML форму данного типа оплаты, для ввода дополнительных параметров
     *
     * @return string
     * @throws \SmartyException
     */
    public function getSettingsFormHtml()
    {
        if ($params = $this->getSettingsFormObject()) {
            $params->getPropertyIterator()->arrayWrap('settings');
            $params->getFromArray((array)$this->service['settings']);
            $params->setFormTemplate(strtolower(str_replace('\\', '_', get_class($this))));
            $module = \RS\Module\Item::nameByObject($this);
            $tpl_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$module.\Setup::$MODULE_TPL_FOLDER;

            return $params->getForm(['service_type' => $this], null, false, null, '%system%/coreobject/tr_form.tpl', $tpl_folder);
        }
        return '';
    }

    /**
     * Дополняет параметры, которые сформировал клиент, другими системными
     *
     * @return array
     */
    protected function prepareParams(array $params)
    {
        return $params + [
                'model' => $this->service['settings']['model'],
                'max_tokens' => (int)$this->config['max_tokens']
            ];
    }

    /**
     * Возвращает новый экземпляр объекта для проведения запросов
     * сразу с установленным логером
     *
     * @param array $config
     * @return Client
     */
    protected function getHttpClientWithLogger($config = [])
    {
        $log = AiLog::getInstance();
        $handlerStack = HandlerStack::create();
        $handlerStack->push($this->getRsLoggerMiddleware($log));

        return new Client([
            'handler' => $handlerStack,
            'headers' => ['Connection' => 'close'],
            'verify' => false,
            ...$config
        ]);
    }

    /**
     * Возвращает обработчик запросов с установленным логером
     *
     * @return \Closure
     */
    protected function getRsLoggerMiddleware($log)
    {
        return function (callable $handler) use ($log) {
            return function (RequestInterface $request, array $options) use ($handler, $log) {
                $headers = [];
                foreach($request->getHeaders() as $key => $value) {
                    $headers[] = $key . ': ' . implode("\n", $value);
                }
                $request_id = uniqid();
                $body = $request->getBody()->getContents();

                //Приводим JSON запроса в читаемый вид
                if (($request->getHeader('Content-Type')[0] ?? '') == 'application/json') {
                    $body = json_encode(json_decode($body, true), JSON_UNESCAPED_UNICODE);
                }

                $log->write(t("Запрос(%id): %method %uri \n\nЗаголовки:\n%headers\n\nТело:\n%body", [
                    'id' => $request_id,
                    'method' => $request->getMethod(),
                    'uri' => (string)$request->getUri(),
                    'headers' => implode("\n", $headers),
                    'body' => $body !== '' ? $body : '-нет-',
                ]), AiLog::LEVEL_REQUEST);

                // Отправляем запрос и логируем ответ
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($log, $request_id) {
                        $stream = $response->getBody();

                        if ($response->getStatusCode() != 200) {
                            $content = $response->getBody()->getContents();
                            $error = (($json = json_decode($content)) && isset($json->error)) ? $json->error : $content;

                            throw new \Exception($error, $response->getStatusCode());
                        }

                        if ($stream->isSeekable()) {

                                $log->write(t("Ответ(%id): %status \n%body", [
                                    'id' => $request_id,
                                    'status' => $response->getStatusCode(),
                                    'body' => $response->getBody()->getContents(),
                                ]), AiLog::LEVEL_REQUEST);

                                $response->getBody()->rewind();
                        } else {
                            // Если ответ - поток, логируем его после завершения
                            $response = $response->withBody(new StreamFinishCallback($stream, function($full_response) use ($log, $request_id, $response) {

                                $log->write(t("Полный ответ (%id): %status \n%body", [
                                    'id' => $request_id,
                                    'status' => $response->getStatusCode(),
                                    'body' => $full_response,
                                ]), AiLog::LEVEL_RESPONSE_FULL);

                                $short_response = $this->parseFullGeneratedText($full_response);
                                if ($short_response !== $full_response) {
                                    $log->write(t("Короткий ответ (%id): %status \n%body", [
                                        'id' => $request_id,
                                        'status' => $response->getStatusCode(),
                                        'body' => $short_response,
                                    ]), AiLog::LEVEL_RESPONSE_SHORT);
                                }
                            }));
                        }

                        return $response;
                    }
                )
                ->then(null, function (\Exception $e) use ($log, $request_id) {
                    $log->write(t("Ошибка(%id): %status %message", [
                        'id' => $request_id,
                        'status' => $e->getCode(),
                        'message' => $e->getMessage()
                    ]), AiLog::LEVEL_REQUEST);

                    throw $e;
                });
            };
        };
    }

    /**
     * Устанавливает дополнительные параметры для ORM-объекта статистики,
     * который будет создан по завершении генерации текста
     *
     * @return $this
     */
    public function setStatisticParams($params)
    {
        $this->statistic_params = $params;
        return $this;
    }

    /**
     * Сохраняет информацию в базе данных об использованных токенах для генерации
     *
     * @param integer $input_text_tokens Кол-во токенов в запросе
     * @param integer $completion_tokens Кол-во токенов в ответе
     *
     * @return bool
     */
    public function saveStatistic($input_text_tokens, $completion_tokens)
    {
        $statistic = new Statistic();
        $statistic->getFromArray($this->getStatisticParams());
        $statistic['service_id'] = $this->service['id'];
        $statistic['date_of_create'] = date('Y-m-d H:i:s');
        $statistic['input_text_tokens'] = $input_text_tokens;
        $statistic['completion_tokens'] = $completion_tokens;
        $statistic['total_tokens'] = $input_text_tokens + $completion_tokens;
        return $statistic->insert();
    }

    /**
     * Возвращает дополнительные параметры для ORM-объекта статистики
     *
     * @return array
     */
    public function getStatisticParams()
    {
        return $this->statistic_params;
    }

    /**
     * Возвращает сгенерированный текст, если обнаружены куски SSE сообщений с необходимым JSON.
     * Если необходимый формат JSON не найден, то возвращается полный ответ $full_response в исходном виде
     *
     * @param string $full_response
     * @return string
     */
    protected function parseFullGeneratedText($full_response)
    {
        //Лог SSE протокола
        if (preg_match_all('/^data: (.*)$/um', $full_response, $matches)) {
            $text = '';
            foreach ($matches[1] as $json_chunk) {
                $json_chunk = trim($json_chunk);
                if ($json_chunk == '[DONE]') continue;

                if ($data = json_decode($json_chunk)) {
                    if (isset($data->choices[0]->delta->content)) {
                        $text .= $data->choices[0]->delta->content;
                    }
                } else {
                    return $full_response;
                }
            }
            return trim($text);
        }
        //Лог формата YandexGPT
        elseif (preg_match_all('/^(\{"result":.*)$/um', $full_response, $matches)) {
            $text = '';
            foreach ($matches[1] as $json_chunk) {
                $json_chunk = trim($json_chunk);
                if ($data = json_decode($json_chunk)) {
                    $text = $data->result->alternatives[0]->message->text;
                } else {
                    return $full_response;
                }
            }
            return trim($text);
        }

        return $full_response;
    }
}