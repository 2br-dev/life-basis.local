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
use Support\Model\TopicApi;

/**
* Удаляет тему переписки
*/
class DeleteTopic extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

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
            self::RIGHT_LOAD => t('Загрузка списка объектов')
        ];
    }

    /**
     * Возвращает общее число объектов для данной выборки
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @return integer
     */
    public function getResultCount($dao)
    {
        $q = clone $dao->queryObj();
        $q->limit(null)
            ->orderby(null)
            ->select = 'COUNT(DISTINCT '.$dao->defAlias().'.'.$dao->getIdField().') as cnt';

        return $q->exec()->getOneField('cnt', 0);
    }

    /**
     * Возвращает список объектов
     *
     * @param TopicApi $dao - API списка обращений
     * @param integer $page - номер страницы
     * @param integer $pageSize - количество элементов
     * @return array
     */
    public function getResult($dao, $user_id, $page = 1, $pageSize = 20)
    {
        $dao->clearFilter();
        $dao->setFilter('user_id', $user_id);
        $list = $dao->getList($page, $pageSize);

        if (!empty($list)){
            $dao->getElement()->getPropertyIterator()->append([
                'last_message' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Последнее сообщение переписки'),
                    'appVisible' => true,
                ]),
            ]);

            foreach ($list as $key => $topic) {
                $last_message =  $topic->getLastMessage();
                if ($last_message) {
                    $last_message->getPropertyIterator()->append([
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

                    $last_message['user_name'] = $last_message->getUserName();
                    $last_message['is_system'] = $last_message->isSystemMessage();

                    $item_attachments = $last_message->getAttachments();
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
                        $last_message['attachments'] = $attachments;
                    }
                    $list[$key][ 'last_message' ] = \ExternalApi\Model\Utils::extractOrm( $last_message );
                }
            }
        }

        return [
            'response' => [
                'summary' => [
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'total' => count( $list ),
                ],
                'list' => \ExternalApi\Model\Utils::extractOrmList( $list ),
            ]
        ];
    }

    /**
     * Удаляет тему переписки
     *
     * @param string $token Авторизационный token
     * @param integer $topic_id ID обращения
     *
     * @example GET /api/methods/support.deleteTopic?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&topic_id=2
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "summary": {
     *              "page": "1",
     *              "pageSize": "20",
     *              "total": "2"
     *          },
     *          "list": [
     *              {
     *                  "id": "1",
     *                  "title": "Обращение номер один",
     *                  "number": "111111",
     *                  "user_id": "1",
     *                  "msgcount": "6",
     *                  "newcount": "1",
     *                  "status": "answered"
     *              },
     *              ...
     *          ]
     *      }
     * }
     * </pre>
     *
     * @return array Возвращает список обращений в поддержку
     */
    protected function process($token, $topic_id)
    {
        if ($user = $this->token->getUser()) {
            $topic_api = new TopicApi();
            $topic_api->setFilter('id', $topic_id);
            $is_admin = $this->token->getApp()->getId() == 'store-management' ? 1 : 0;
            if (!$is_admin) {
                $topic_api->setFilter('user_id', $user['id']);
            }

            $topic = $topic_api->getFirst();

            if ($topic) {
                if ($topic->delete()) {
                    return  $this->getResult($topic_api, $user->id);
                } else {
                    throw new ApiException(t('При удалении возникла ошибка: %0', [$topic->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
                }

            }else {
                throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
            }
        }else {
            throw new ApiException(t('Пользователь с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }
    }
}