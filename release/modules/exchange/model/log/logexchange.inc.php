<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Exchange\Model\Log;

use RS\Log\AbstractLog;

/**
 * Класс логирования обмена данными с 1С
 */
class LogExchange extends AbstractLog
{
    const LEVEL_REQUEST = 'request';
    const LEVEL_LOCK = 'lock';
    const LEVEL_TASK = 'task';
    const LEVEL_CATALOG_IMPORT = 'catalog_import';
    const LEVEL_CATEGORY_IMPORT = 'category_import';
    const LEVEL_TYPE_COST_IMPORT = 'type_cost_import';
    const LEVEL_WAREHOUSE_IMPORT = 'warehouse_import';
    const LEVEL_PROPERTY_IMPORT = 'property_import';
    const LEVEL_PRODUCT_IMPORT = 'product_import';
    const LEVEL_PRODUCT_IMPORT_DETAIL = 'product_import_detail';
    const LEVEL_OFFER_IMPORT = 'offer_import';
    const LEVEL_OFFER_IMPORT_DETAIL = 'offer_import_detail';
    const LEVEL_ORDER_EXPORT = 'order_export';
    const LEVEL_ORDER_EXPORT_DETAIL = 'order_export_detail';
    const LEVEL_ORDER_IMPORT = 'order_import';

    /**
     * Возвращает идентификатор класса логирования
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'exchange';
    }

    /**
     * Возвращает название класса логирования
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('Обмен данными с 1С');
    }

    /**
     * Возвращает список допустимых уровней лог-записей
     *
     * @return string[]
     */
    protected function selfLogLevelList(): array
    {
        return [
            self::LEVEL_REQUEST => t('Запросы'),
            self::LEVEL_LOCK => t('Блокировка'),
            self::LEVEL_TASK => t('Задачи'),
            self::LEVEL_CATALOG_IMPORT => t('Импорт каталога'),
            self::LEVEL_CATEGORY_IMPORT => t('Импорт категории'),
            self::LEVEL_TYPE_COST_IMPORT => t('Импорт типов цен'),
            self::LEVEL_WAREHOUSE_IMPORT => t('Импорт складов'),
            self::LEVEL_PROPERTY_IMPORT => t('Импорт характеристик'),
            self::LEVEL_PRODUCT_IMPORT => t('Импорт товаров'),
            self::LEVEL_PRODUCT_IMPORT_DETAIL => t('Импорт товаров (детали)'),
            self::LEVEL_OFFER_IMPORT => t('Импорт комплектаций'),
            self::LEVEL_OFFER_IMPORT_DETAIL => t('Импорт комплектаций (детали)'),
            self::LEVEL_ORDER_EXPORT => t('Экспорт заказов'),
            self::LEVEL_ORDER_EXPORT_DETAIL => t('Экспорт заказов (детали)'),
            self::LEVEL_ORDER_IMPORT => t('Импорт заказов'),
        ];
    }
}
