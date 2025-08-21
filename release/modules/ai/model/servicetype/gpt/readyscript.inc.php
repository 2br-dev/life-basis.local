<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\ServiceType\Gpt;

use Ai\Model\Exception;
use Ai\Model\ServiceType\AbstractServiceType;
use Ai\Model\ServiceType\BalanceInterface;
use Ai\Model\ServiceType\ServiceChatResponse;
use GuzzleHttp\Psr7\Request;
use Main\Model\LicenseApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use Traversable;
use RS\Orm\Type;

/**
 * Класс-обертка для работы ReadyScript с сервисом Yandex GPT.
 */
class ReadyScript extends AbstractServiceType
    implements BalanceInterface
{

    /**
     * Возвращает идентификатор сервиса
     *
     * @return string
     */
    public static function getId()
    {
        return 'readyscript';
    }

    /**
     * Возвращает название сервиса
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('ReadyScript');
    }

    /**
     * Возвращает описание сервиса
     *
     * @return string
     */
    public function getDescriptionHtml()
    {
        return t('Встроенный в ReadyScript GPT-сервис');
    }

    /**
     * Возвращает объект, для отображения формы для настроек сервиса
     *
     * @return FormObject
     */
    public function getSettingsFormObject()
    {
        return new FormObject(new PropertyIterator([]));
    }

    /**
     * Возвращает инициализированный объект библиотеки клиента сервиса
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        $license_api = new LicenseApi();
        $main_license_hash =  $license_api->getMainLicenseHash();

        if (!$main_license_hash && !defined('CLOUD_UNIQ')) {
            throw new Exception(t('Установите лицензионный ключ для платформы ReadyScript, чтобы пользоваться AI–ассистентом'));
        }

        $client = $this->getHttpClientWithLogger([
            'base_uri' => \Setup::$RS_SERVER_PROTOCOL.'://gpt.'.\Setup::$RS_SERVER_DOMAIN,
            'timeout' => $this->config['timeout_sec'],
            'headers' => [
                'Content-type' => 'application/json',
                'X-ReadyScript-Key' => defined('CLOUD_UNIQ') ? 'cloud-'.CLOUD_UNIQ : 'box-'.$main_license_hash,
                //'Accept' => 'text/event-stream'
            ]
        ]);

        return $client;
    }

    /**
     * Создает стрим для возвращения ответа для одиночной фразы.
     *
     * @param array $params Параметры запроса в формате openAI
     * @return Traversable Должен вернуть генератор
     */
    public function createChatStream(array $params): Traversable
    {
        $messages = [
            'messages' => array_map(function($value) {
                return [
                    'role' => $value['role'],
                    'text' => $value['content']
                ];
            }, ($params['messages'] ?? []))
        ];
        unset($params['messages']);

        if (isset($params['temperature'])) {
            $params['temperature'] = max(0, min(1, $params['temperature']));
        }

        $request = new Request('POST', 'completion', [], json_encode([
            "completionOptions" => [
                "stream" => true,
                ...$params
            ],
            ...$messages
        ], JSON_UNESCAPED_UNICODE));

        $client = $this->getClient();
        $response = $client->send($request, [
            'stream' => true,
        ]);

        return $this->resultGenerator( $this->makeStream($response) );
    }

    /**
     * Обрабатывает чистый ответ от GPT сервиса и формирует ответ во внутреннем формате ReadyScript
     *
     * @param $stream
     * @return \Traversable
     */
    public function resultGenerator($stream)
    {
        $full_text = '';
        foreach($stream as $stream_response) {
            if (isset($stream_response->result->alternatives[0]->message->text)) {
                $new_full_text = $stream_response->result->alternatives[0]->message->text;
                $new_full_text = $this->filterLine($new_full_text);
                $text = mb_substr($new_full_text, mb_strlen($full_text));
                $full_text = $new_full_text;

                //Формируем ответ по внутреннему формату ReadyScript
                $chunk = (new ServiceChatResponse())
                    ->setDeltaText($text)
                    ->setFullText($full_text)
                    ->setIsFinish($stream_response->result->alternatives[0]->status == 'ALTERNATIVE_STATUS_FINAL');

                if ($chunk->isFinish()) {
                    //Добавим к результату стоимость запроса и новый баланс
                    if (isset($stream_response->result->rsRequestCost)) {
                        $chunk->setExtraData([
                            'requestCost' => $stream_response->result->rsRequestCost,
                            'balance' =>  $stream_response->result->rsBalance,
                        ]);
                    }

                    //Сохраняем статистику использованных токенов
                    if (isset($stream_response->result->usage)) {
                        $this->saveStatistic(
                            $stream_response->result->usage->inputTextTokens,
                            $stream_response->result->usage->completionTokens
                        );
                    }
                }

                yield $chunk;
            }
        }
    }


    /**
     * Фильтрует полученный текст от спец.символов
     *
     * @param $string
     * @return array|string|string[]
     */
    function filterLine($string)
    {
        //Необходимо, когда запрашиваем ответ в формате HTML. Особенность Яндекс GPT
        return $string;
    }

    /**
     * Позволяет итерировать поток данных
     *
     * @param $stream
     * @return \Generator
     */
    protected function makeStream(ResponseInterface $response)
    {
        while (!$response->getBody()->eof()) {
            $line = $this->readLine($response->getBody());
            $data = trim($line);
            if ($data !== '') {
                /** @var array{error?: array{message: string|array<int, string>, type: string, code: string}} $response */
                $json = json_decode($data, false, flags: JSON_THROW_ON_ERROR);

                if (isset($json->error)) {
                    throw new Exception($json->error, $response->getStatusCode());
                }

                yield $json;
            }
        }
    }

    /**
     * Читает одну строку данных из потока
     *
     * @return string
     */
    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }

    /**
     * Возвращает список моделей, которые поддерживает данный сервис
     * В ключе идентификатор модели, в значении - название
     *
     * @return array
     */
    public function getModelList(): array
    {
        return [];
    }

    /**
     * Возвращает Баланс пользователя на GPT - сервисе
     *
     * @param bool $cache Зарезервировано, пока не используется
     * @return float|null
     */
    public function getBalance($cache = true)
    {
        $request = new Request('POST', 'getBalance', [], '');

        $client = $this->getClient();
        $response = $client->send($request);
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody()->getContents(), true);
            if ($data !== false) {
                return $data['balance'];
            }
        }

        return null;
    }

    /**
     * Возвращает URL страницы с информацией о пополнении баланса запросов к ИИ
     *
     * @return string
     */
    public function getBalanceRefillUrl()
    {
        return \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/ai/';
    }
}