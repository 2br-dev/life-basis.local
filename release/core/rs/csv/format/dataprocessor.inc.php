<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Format;

use RS\Csv\Format\Type\Csv;
use RS\Csv\Format\Type\Ods;
use RS\Csv\Format\Type\Xls;
use RS\Csv\Format\Type\Xlsx;
use RS\Event\Manager as EventManager;
use RS\Exception;

/**
 * Класс содержит функции по получению списка, а также объекта процессора форматов импорта/экспорта
 */
class DataProcessor
{
    /**
     * Возвращает полный список возможных форматов данных
     *
     * @param bool $cache Не вызывать повторно событие сборки форматов
     * @return AbstractFormatType[]
     */
    public static function getFormatTypes($cache = true)
    {
        static $result;

        if (!$cache || $result === null) {
            //Набор системных форматов
            $result = [
                Csv::getId() => new Csv(),
                Xls::getId() => new Xls(),
                Xlsx::getId() => new Xlsx(),
                Ods::getId() => new Ods()
            ];
            $event_result = EventManager::fire('getImportExportFormatTypes', []);
            foreach($event_result->getResult() as $item) {
                if (!($item instanceof AbstractFormatType)) {
                    throw new Exception(t('Формат импорта/экспорта данных должен быть наследником RS\Csv\Format\AbstractFormatType'));
                }
                $result[$item->getId()] = $item;
            }
        }
        return $result;
    }

    /**
     * Возвращает список названий форматов данных
     *
     * @param bool $cache Не вызывать повторно событие сборки форматов
     * @return string[]
     */
    public static function getFormatTypeTitles($cache = true)
    {
        $result = [];
        $types = self::getFormatTypes($cache);
        foreach($types as $id => $item) {
            $result[$id] = $item->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает объект процессора импорта/экспорта данных
     *
     * @param string $id Идентификатор формата данных
     * @param bool $cache Не вызывать повторно событие сборки форматов
     * @return AbstractFormatType
     * @throws Exception
     */
    public static function getFormatTypeObject($id, $cache = true)
    {
        $types = self::getFormatTypes($cache);
        if (isset($types[$id])) {
            return clone $types[$id];
        }

        throw new Exception(t('Не обнаружен класс формата импорта/экспорта данных %0', [$id]));
    }

    /**
     * Возвращает полный список допустимых расширений файлов для импорта данных
     *
     * @param bool $cache Не вызывать повторно событие сборки форматов
     * @return array
     * @throws Exception
     */
    public static function getAllowFormatExtensions($cache = true)
    {
        $result = [];
        $types = self::getFormatTypes($cache);
        foreach($types as $item) {
            $result[] = $item->getFileExtension();
        }

        return $result;
    }
}