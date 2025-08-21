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
use Support\Model\Orm\Topic;
use Support\Model\Platform\PlatformMobileSiteApp;
use Support\Model\Platform\PlatformSite;
use Support\Model\TopicApi;

/**
* Создает тему переписки - администратором
*/
class CreateAdminTopic extends AbstractAuthorizedMethod
{
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

    public
        /**
         * @var Api
         */
        $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = new TopicApi();
    }

    /**
     * Создает тему переписки
     * Возвращает объект созданной переписки
     *
     * @param string $token Авторизационный token
     * @param string $title Тема обращения
     * @param string $message Сообщение
     * @param integer $user_id ID пользователя
     * @param integer $manager_id ID ответственного менеджера
     *
     * @example GET /api/methods/support.createAdminTopic?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&message=Помогите&title=Помогите&user_id=2&manager_id=1
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "success": true,
     *          "topic": {
     *              "id": "25",
     *              "title": "Помогите",
     *              "number": "752508",
     *              "user_id": "2",
     *              "manager_id": 1,
     *              "created": "2024-01-18 16:37:29",
     *              "updated": "2024-01-18 16:37:29",
     *              "msgcount": "1",
     *              "newcount": "0",
     *              "status": "open"
     *           }
     *      }
     * }
     * </pre>
     *
     * @return array
     */
    protected function process($token, $title = '', $message = '', $user_id = null, $manager_id = null)
    {
        $topic = new Topic();
        $topic->platform = PlatformSite::PLATFORM_ID;
        $topic->_admin_creation_ = true;
        $topic->___first_message_->setVisible(true);
        $topic->___first_message_
            ->setChecker('chkEmpty', t('Сообщение обязательное поле'));

        $save_data = [
            'title' => $title,
            'user_id' => $user_id,
            'manager_id' => $manager_id,
            '_first_message_' => $message
        ];

        if ($topic->save(null, [], $save_data)) {
            GetTopicList::appendDynamicProperties($topic);
            GetTopicList::appendDynamicValues($topic);

            return [
                'response' => [
                    'success' => true,
                    'topic' => \ExternalApi\Model\Utils::extractOrm($topic)
                ]
            ];
        }else {
            throw new ApiException(t($topic->getErrorsStr()), ApiException::ERROR_WRITE_ERROR);
        }
    }
}