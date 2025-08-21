<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Platform;

use RS\Orm\AbstractObject;
use Support\Model\Orm\Support;
use Support\Model\Utils;

/**
 * Класс описывает платформу - Мобильное приложение.
 */
class PlatformMobileSiteApp extends AbstractPlatform
{
    const PLATFORM_ID = 'mobilesiteapp';
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
        return t('Мобильное приложение');
    }

    /**
     * Обработчик сохранения сообщения.
     * Отправляет PUSH уведомление пользователю о том, что администратор ответил в тикете
     *
     * @param Support $message Объект сообщения
     * @param string $save_flag флаг операции
     *
     * @return void
     */
    public function onSaveMessage(Support $message, $save_flag)
    {
        if ($message['is_admin'] && $save_flag == AbstractObject::INSERT_FLAG) {
            Utils::sendNewMessagePush($message);
        }
    }
}