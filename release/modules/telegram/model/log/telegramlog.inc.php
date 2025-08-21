<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логов от платформы Telegram
 */
class TelegramLog extends AbstractLog
{
    const LEVEL_TG_DEBUG = 'tg_debug';
    const LEVEL_TG_ERROR = 'tg_error';
    const LEVEL_TG_UPDATE = 'tg_update';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'telegram';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Логи Telegram ботов');
    }


    /**
     * Возвращает список допустимых уровней лог-записей
     * Уровни логирования используются для настройки детальности логирования и фильтрации записей при просмотре лог-файлов
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_INFO => t('Информационные сообщения ReadyScript'),
            self::LEVEL_TG_DEBUG => t('Отладочная информация Telegram'),
            self::LEVEL_TG_UPDATE => t('Обновления от Telegram'),
            self::LEVEL_TG_ERROR => t('Ошибки Telegram'),
        ];
    }
}