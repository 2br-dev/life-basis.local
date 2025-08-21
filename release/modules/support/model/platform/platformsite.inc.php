<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Platform;

use Alerts\Model\Manager;
use RS\Orm\AbstractObject;
use Support\Model\Orm\Support;
use Support\Model\Utils;
use Users\Model\Orm\User;

/**
 * Класс описывает платформу - Сайт.
 * Обычная поддержка из личного кабинета на сайте
 */
class PlatformSite extends AbstractPlatform
{
    const PLATFORM_ID = 'site';
    /**
     * Возвращает идентификатор платформы
     *
     * @return string
     */
    function getId()
    {
        return self::PLATFORM_ID;
    }

    /**
     * Возвращает название платформы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Сайт');
    }

    /**
     * Обработчик сохранения сообщения.
     * Отправляет сообщение пользователю о том, что администратор ответил в тикете
     *
     * @param Support $message Объект сообщения
     * @param string $save_flag флаг операции
     *
     * @return void
     */
    public function onSaveMessage(Support $message, $save_flag)
    {
        if ($message['is_admin'] && $save_flag == AbstractObject::INSERT_FLAG) {
            $notice = new \Support\Model\Notice\User();
            $notice->init($message);
            $notice->setUser(new User($message->getTopic()->user_id)); //Установка пользователя
            Manager::send($notice);
            Utils::sendNewMessagePush($message);
        }
    }
}