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
use ExternalApi\Model\Utils;
use Support\Model\Orm\Topic;

/**
 * Метод API - закрыть тикет
 */
class CloseTopic extends AbstractAuthorizedMethod
{
    const RIGHT_CLOSE = 1;

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
            self::RIGHT_CLOSE => t('Закрытие тикета')
        ];
    }

    /**
     * Переводит тему переписки в статус "закрыт".
     *
     * @param string $token Авторизационный токен
     * @param integer $topic_id ID тикета
     *
     * @example GET /api/methods/support.closeTopic?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&topic_id=1
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true,
                "topic": {
                    "id": "229",
                    "title": "Тикет",
                    "number": "028049",
                    "user_id": "91",
                    "manager_id": "0",
                    "created": "2024-09-18 18:51:11",
                    "updated": "2024-10-06 15:17:59",
                    "msgcount": "3",
                    "newcount": "0",
                    "status": "closed",
                    "last_message": {
                        "id": "436",
                        "dateof": "2024-10-06 15:17:59",
                        "updated": null,
                        "message": "Отправил",
                        "is_admin": "1",
                        "external_id": "17282170790281u038p75y40fsn8evpa0qrr88qy",
                        "attachments": null,
                        "is_system": false,
                        "user_name": "Иванов Артем Петрович"
                    }
                }
            }
        }
     * </pre>
     *
     * @return array Возвращает объект темы переписки
     */
    protected function process($token, $topic_id)
    {
        $topic = Topic::loadByWhere([
            'id' => $topic_id
        ]);

        if ($topic['id']) {
            $topic['status'] = Topic::STATUS_CLOSED;
            $topic->update();

            GetTopicList::appendDynamicProperties($topic);
            GetTopicList::appendDynamicValues($topic);

            return [
                'response' => [
                    'success' => true,
                    'topic' => Utils::extractOrm($topic)
                ]
            ];
        }

        throw new ApiException(t('Тема переписки не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}