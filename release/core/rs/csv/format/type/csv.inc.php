<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Format\Type;

use RS\Csv\Format\AbstractFormatType;

/**
 * Класс формата экспорта данных - CSV
 */
class Csv extends AbstractFormatType
{
    protected $options = [
        'delimiter' => ',',
        'enclosure' => '"'
    ];

    protected $fp;

    /**
     * Открывает файл для чтения или записи.
     *
     * @param string $filename полный путь к файлу
     * @return bool Возвращает true, если файл может быть успешно открыт
     */
    function openFile($filename, $mode)
    {
        @ini_set('auto_detect_line_endings', true);
        $file_mode = ($mode == self::MODE_WRITE) ? 'w' : 'r';
        $this->fp = fopen($filename, $file_mode);
        return $this->fp == true;
    }

    /**
     * Закрывает файл
     *
     * @return bool
     */
    function closeFile()
    {
        return fclose($this->fp);
    }

    /**
     * Записывает одну строку данных в файл
     *
     * @param array $data Данные для одной строки для записи в файл
     * @return mixed
     */
    function writeLine($data)
    {
        return fputcsv($this->fp, $data, $this->getOption('delimiter'), $this->getOption('enclosure'));
    }

    /**
     * Читает одну строку данных из файла
     *
     * @return array
     */
    function readLine()
    {
        return fgetcsv($this->fp, null, $this->getOption('delimiter'), $this->getOption('enclosure'));
    }

    /**
     * Возвращает на какой последней позиции все остановилось
     *
     * @return integer
     */
    function tellPosition()
    {
        return ftell($this->fp);
    }

    /**
     * Перемещает указатель на нужную позицию в файле
     *
     * @param integer $position позиция, которая была возвращена через tellPosition()
     * @return mixed
     */
    function seekPosition($position)
    {
        return fseek($this->fp, $position);
    }

    /**
     * Возвращает расширение файла без точки, которое имеет данный формат
     *
     * @return string
     */
    function getFileExtension()
    {
        return 'csv';
    }

    /**
     * Возвращает строковый идентификатор формата данных
     *
     * @return string
     */
    static function getId()
    {
        return 'csv';
    }

    /**
     * Возвращает название формата данных
     *
     * @return string
     */
    static function getTitle()
    {
        return 'CSV';
    }

    /**
     * Метод при необходимости подготавливает данные для ячейки.
     * В случае с CSV, данные предварительно нужно переконвертировать в выбранный в настройках формат
     *
     * @param string $string_in_utf8 Строка данных в кодировке UTF-8
     * @param string $csv_charset Кодировка, выбранная для CSV в настройках системного модуля
     * @return false|string
     */
    function prepareExportCellDataCharset($string_in_utf8, $csv_charset)
    {
        // Проверка кодировки исходной строки
        if (!mb_check_encoding($string_in_utf8, 'UTF-8')) {
            return false;
        }

        return iconv('utf-8', $csv_charset, $string_in_utf8);
    }

    /**
     * Данный метод должен вернуть строку всегда в UTF-8 формате
     *
     * @param $string
     * @param $wait_charset
     * @return false|string
     */
    function prepareImportCellDataCharset($string, $csv_charset)
    {
        return iconv($csv_charset, 'utf-8', $string);
    }
}