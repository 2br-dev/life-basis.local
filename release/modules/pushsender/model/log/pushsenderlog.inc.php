<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования модуля PushSender
 */
class PushSenderLog extends AbstractLog
{
    const ID = 'pushsender';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::ID;
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Лог отправки Push-уведомлений');
    }
}