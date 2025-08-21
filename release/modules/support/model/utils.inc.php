<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

use RS\Config\Loader;
use Support\Model\Orm\Topic;
use Support\Model\Push\NewMessage;

class Utils
{
    /**
     * Отправляет PUSH уведомление пользователю о том, что администратор ответил в тикете
     *
     * @param $message
     * @return void
     * @throws \PushSender\Model\Exception
     */
    public static function sendNewMessagePush($message)
    {
        $mobilesiteapp_config = Loader::byModule('mobilesiteapp');
        $topic = new Topic($message['topic_id']);

        $lastAccessDateTime = strtotime($topic['last_messages_request']);
        $currentDateTime = time();

        if ($currentDateTime >= ($lastAccessDateTime + ($mobilesiteapp_config['minutes_to_send_push_new_message'] * 60))) {
            $push = new NewMessage();
            $push->init($message, $topic);
            $push->send();
        }
    }
}
