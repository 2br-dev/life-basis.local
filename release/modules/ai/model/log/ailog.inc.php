<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования взаимодействий с ИИ
 */
class AiLog extends AbstractLog
{
    const LEVEL_REQUEST = 'request';
    const LEVEL_RESPONSE_SHORT = 'response_short';
    const LEVEL_RESPONSE_FULL = 'response_full';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'ailog';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Логирование модуля AI-ассистент');
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
            self::LEVEL_REQUEST => t('Запросы к ИИ'),
            self::LEVEL_RESPONSE_FULL => t('Полный ответ ИИ'),
            self::LEVEL_RESPONSE_SHORT => t('Короткий ответ ИИ'),
            self::LEVEL_INFO => t('Разное'),
        ];
    }
}