<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования онлайн-касс
 */
class LogTrueApi extends AbstractLog
{
    const LEVEL_CDN_INFO = 'cdn_info';
    const LEVEL_CHECK_CODES = 'check_codes';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'true_api';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Запросы к системе ЧестныйЗнак');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_CDN_INFO => t('Запрос доступных хостов'),
            self::LEVEL_CHECK_CODES => t('Проверка кодов маркировки'),
        ];
    }
}
