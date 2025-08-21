<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Antivirus\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования антивируса
 */
class LogAntivirus extends AbstractLog
{
    const LEVEL_SELF_UPDATE = 'self_update';
    const LEVEL_SERVER_API = 'server_api';

    /**
     * Singleton, необходимо использовать ::getInstance()
     * для создания объекта
     */
    protected function __construct()
    {
        parent::__construct(0);
        $this->setSiteId(0);
    }

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'antivirus';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Антивирус');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_SELF_UPDATE => t('Обновления антивируса'),
            self::LEVEL_SERVER_API => t('Запросы к серверу ReadyScript')
        ];
    }
}
