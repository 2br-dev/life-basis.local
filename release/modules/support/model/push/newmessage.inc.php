<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Push;

use PushSender\Model\Firebase\Push\GoogleFCMPushNotice;
use PushSender\Model\InterfaceDirectPushTokensNotice;
use RS\Site\Manager as SiteManager;

/**
 * Push уведомление пользователю о новом сообщении в поддержке.
 */
class NewMessage extends GoogleFCMPushNotice implements InterfaceDirectPushTokensNotice
{
    public $ids;
    public $topic;
    public $message;

    /**
     * Инициализация PUSH уведомления
     *
     * @param $message - объект сообщения
     * @param $topic - объект темы сообщения
     */
    public function init($message, $topic)
    {
        $this->topic = $topic;
        $this->message = $message;
        $this->ids = [$this->topic['user_id']];
    }

    /**
     * Возвращает описание уведомления для внутренних нужд системы и
     * отображения в списках админ. панели
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Администратор ответил в тикете(пользователю)');
    }

    /**
     * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
     *
     * @return string
     */
    public function getAppId()
    {
        return 'mobilesiteapp';
    }

    /**
     * Возвращает одного или нескольких получателей по пользователям
     *
     * @return array
     */
    public function getRecipientUserIds()
    {
        return [];
    }

    /**
     * Возвращает массив PUSH токенов устройств, которым нужно отправить уведомление
     * @return array
     */
    public function getRecipientPushTokens()
    {
        if (!empty($this->ids)){
            return \RS\Orm\Request::make()
                ->from(new \PushSender\Model\Orm\PushToken())
                ->whereIn('user_id', $this->ids)
                ->where([
                    'app' => $this->getAppId()
                ])
                ->objects();
        }
        return [];
    }

    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        return t('Администратор ответил в тикете');
    }

    /**
     * Возвращает текст Push уведомления
     *
     * @return string
     */
    public function getPushBody()
    {
        $message = strip_tags($this->message['message']);
        return strlen($message) > 127 ? mb_substr($message, 0, 128) . '...' : $message;
    }

    /**
     * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
     *
     * @return array
     */
    public function getPushData()
    {
        $site = SiteManager::getSite();

        return [
            'topic_id' => $this->topic['id'],
            'site_uid' => $site->getSiteHash(),
            'soundname' => "default",

            'content-available' => "1",
            'action' => "ViewTopicPage",
            'params' => json_encode([
                'topic' => [
                    'id' => $this->topic['id'],
                    'title' => $this->topic['title'],
                    'number' => $this->topic['number'],
                    'created' => date("d.m.Y", strtotime($this->topic['created'])),
                    'updated' => date("d.m.Y", strtotime($this->topic['updated']))
                ]
            ]),
        ];
    }

    /**
     * Возвращает click_action для данного уведомления
     *
     * @return string
     */
    public function getPushClickAction()
    {
        return false;
    }
}