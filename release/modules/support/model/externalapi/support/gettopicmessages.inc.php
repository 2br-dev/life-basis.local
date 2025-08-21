<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\ExternalApi\Support;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Support\Model\Api;
use Support\Model\Orm\Support as OrmSupport;
use Support\Model\TopicApi;

/**
* Возвращает переписку в рамках одной темы
*/
class GetTopicMessages extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

    public
        /**
         * @var Api
         */
        $api,
        /**
         * @var TopicApi
         */
        $topic_api;

    public function __construct()
    {
        parent::__construct();
        $this->api = new Api();
        $this->topic_api = new TopicApi();
    }

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return string[] - [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка объектов.')
        ];
    }

    /**
     * Добавляет секцию с перепиской в данной теме
     *
     * @param $result
     * @return array
     */
    protected function addMessagesSection($result)
    {
        if (isset($result['response']['topic']['id'])) {
            $this->api->setFilter('topic_id', $result['response']['topic']['id']);
            $this->api->setFilter('message_type', OrmSupport::TYPE_ADMIN_SYSTEM_MESSAGE, '!=');
            $this->api->setOrder('dateof');
            $list = $this->api->getList();

            if ($list) {
                $messages = [];

                foreach ($list as $key =>  $item) {
                    $item->getPropertyIterator()->append([
                        'is_system' => new \RS\Orm\Type\Integer([
                            'maxLength'   => 1,
                            'description' => t('Это системное сообщение'),
                            'appVisible' => true
                        ]),
                        'attachments' => new \RS\Orm\Type\ArrayList([
                            'description' => t('Файлы'),
                            'appVisible' => true
                        ]),
                        'user_name' => new \RS\Orm\Type\Varchar([
                            'description' => t('Имя отправителя'),
                            'appVisible' => true
                        ]),
                    ]);

                    $messages[$key] = $item;
                    $messages[$key]['user_name'] = $item->getUserName();
                    $messages[$key]['is_system'] = $item->isSystemMessage();

                    $item_attachments = $item->getAttachments();
                    $attachments = [];
                    if ($item_attachments) {
                        foreach ($item_attachments as $attachment) {
                            $attachment->getPropertyIterator()->append([
                                'url' => new \RS\Orm\Type\Varchar([
                                    'description' => t('Ссылка на скачивание'),
                                    'appVisible' => true
                                ]),
                            ]);
                            $attachment['url'] = $attachment->getHashedUrl(true);

                            $attachments[] = \ExternalApi\Model\Utils::extractOrm($attachment);
                        }
                    }

                    if (!empty($attachments)) {
                        $messages[$key]['attachments'] = $attachments;
                    }
                }

                if (!empty($messages)) {
                    $result['response']['messages'] = \ExternalApi\Model\Utils::extractOrmList($messages);
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает переписку в рамках одной темы
     *
     * @param string $token Авторизационный token
     * @param integer $topic_id ID обращения
     * @param array $sections Секции с данными, которые следует включить в ответ. Возможные значения:
     * <b>topic</b> - Тема обращения
     * <b>messages</b> - Сообщения данной темы
     *
     * @example GET /api/methods/support.getTopicMessages?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&topic_id=1
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "topic": {
     *                  "id": "1",
     *                  "title": "Обращение номер один",
     *                  "number": "111111",
     *                  "user_id": "1",
     *                  "msgcount": "6",
     *                  "newcount": "1",
     *                  "status": "answered"
     *              },
     *              "messages": [
     *                  {
     *                      "id": "1",
     *                      "dateof": "2024-01-09 11:22:31",
     *                      "updated": null,
     *                      "message": "Вопрос",
     *                      "is_admin": "0",
     *                      "external_id": null,
     *                      "attachments": []
     *                  },
     *                  {
     *                      "id": "2",
     *                      "dateof": "2024-01-09 11:24:32",
     *                      "updated": null,
     *                      "message": "Ответ с вложением",
     *                      "is_admin": "1",
     *                      "external_id": null,
     *                      "attachments": [
     *                          {
     *                              "id": "2",
     *                              "name": "1.png",
     *                              "description": null,
     *                              "ip": "127.0.0.1",
     *                              "date_of_upload": "2024-01-09 11:24:31",
     *                              "url": "https://site.ru/download-file/111.png"
     *                          }
     *                      ]
     *                  },
     *                  {
     *                      "id": "3",
     *                      "dateof": "2024-01-09 11:24:45",
     *                      "updated": null,
     *                      "message": "Вопрос со вложением",
     *                      "is_admin": "0",
     *                      "external_id": null,
     *                      "attachments": [
     *                          {
     *                              "id": "3",
     *                              "name": "2.png",
     *                              "description": null,
     *                              "ip": "127.0.0.1",
     *                              "date_of_upload": "2024-01-09 11:24:44",
     *                              "url": "https://site.ru/download-file/222.png"
     *                          }
     *                      ]
     *                  },
     *                  ...
     *              ]
     *      }
     * }
     * </pre>
     *
     * @return array
     * @throws ApiException
     */
    protected function process($token, $topic_id, $sections = ['messages'])
    {
        $topic = $this->topic_api->getOneItem($topic_id);

        if ($topic && $topic['user_id'] == $this->token['user_id']) {
            $result = [];

            $result['response']['topic'] = \ExternalApi\Model\Utils::extractOrm($topic);
            $this->api->markViewedList($topic['id'], true);

            if (in_array('messages', $sections)) {
                $result =  $this->addMessagesSection($result);
            }

            $topic['last_messages_request'] = date('Y-m-d H:i:s');
            $topic->update();

            if (!empty($result)) {
                return  $result;
            }
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}