<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс описывает Лог класса Support
 */
class LogSupport extends AbstractLog
{
    const ID = 'support-log';
    const LEVEL_PLATFORM_MAIL = 'platform-mail';

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
        return t('Служба поддержки');
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
            self::LEVEL_PLATFORM_MAIL => t('Лог поддержки через почту'),
        ];
    }
}