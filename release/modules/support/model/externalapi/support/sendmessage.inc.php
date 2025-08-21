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
use Support\Model\Orm\Support;
use Support\Model\Platform\PlatformMobileSiteApp;

/**
* Добавление сообщения к переписке
*/
class SendMessage extends AbstractAuthorizedMethod
{
    public
        /**
         * @var Api
         */
        $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = new Api();
    }

    const RIGHT_ADD = 1;

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
            self::RIGHT_ADD => t('Создание переписки')
        ];
    }

    /**
     * Возвращает объект
     *
     * @return array
     */
    public function getResult($support_item)
    {
        $support_item->getPropertyIterator()->append([
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

        $support_item['user_name'] = $support_item->getUserName();
        $support_item['is_system'] = $support_item->isSystemMessage();

        $item_attachments = $support_item->getAttachments();
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
            $support_item['attachments'] = $attachments;
        }

        return \ExternalApi\Model\Utils::extractOrm($support_item);
    }

    /**
     * Добавляет сообщение к переписке
     *
     * @param string $token Авторизационный token
     * @param integer $topic_id ID обращения
     * @param string $message Текст сообщения
     * @param string $external_id Внешний ID
     * @param array $attachments Вложения (массив должен содержать публичные HASH данные)
     *
     * @example GET /api/methods/support.sendMessage?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&topic_id=1
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "success": true,
     *          "support": {
     *              "id": 2,
     *              "dateof": "2024-01-17 11:50:40",
     *              "updated": null,
     *              "message": "Добрый день! Уточните, пожалуйста, ...",
     *              "is_admin": 0,
     *              "external_id": null,
     *              "attachments": [],
     *              "is_system": false,
     *              "user_name": "Иванов Иван Иванович"
     *          }
     *      }
     * }
     * </pre>
     *
     * @return array Возвращает объект сообщения
     * @throws ApiException
     */
    protected function process($token, $topic_id, $message, $external_id, $attachments = [])
    {
        if ($user = $this->token->getUser()) {
            $support = $this->api->getSupportByExternalId($external_id);
            if ($support) {
                return [
                    'response' => [
                        'success' => true,
                        'support' => $this->getResult($support)
                    ]
                ];
            }else {
                $is_admin = $this->token->getApp()->getId() == 'store-management' ? 1 : 0;
                $support_item = $this->api->getNewElement();
                $support_item->escapeAll(true);

                $save_data = [
                    'user_id' => $user['id'],
                    'is_admin' => $is_admin,
                    'topic_id' => $topic_id,
                    'message' => $message,
                    'external_id' => $external_id,
                    'message_type' => $is_admin ? Support::TYPE_ADMIN_MESSAGE : Support::TYPE_USER_MESSAGE,
                    'topic_platform' => PlatformMobileSiteApp::PLATFORM_ID
                ];

                if ($attachments) {
                    $save_data['attachments'] = $attachments;
                }

                if ($support_item->save(null, $save_data)) {
                    return [
                        'response' => [
                            'success' => true,
                            'support' => $this->getResult($support_item)
                        ]
                    ];
                }else {
                    throw new ApiException(t($support_item->getErrorsStr()), ApiException::ERROR_WRITE_ERROR);
                }
            }
        }else {
            throw new ApiException(t('Пользователь с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }
    }
}