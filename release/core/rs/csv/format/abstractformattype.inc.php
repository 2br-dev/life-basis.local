<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Format;

/**
 * Базовый класс для формата импорта/экспорта данных
 */
abstract class AbstractFormatType
{
    const MODE_WRITE = 'write';
    const MODE_READ = 'read';

    protected $options = [];

    /**
     * Открывает файл для чтения или записи.
     *
     * @param string $filename полный путь к файлу
     * @param string $mode режим, для которого открывается файл - чтение или запись.
     * @return bool Возвращает true, если файл может быть успешно открыт
     */
    abstract function openFile($filename, $mode);

    /**
     * Закрывает файл
     *
     * @return bool
     */
    abstract function closeFile();

    /**
     * Записывает одну строку данных в файл
     *
     * @param array $data Данные для одной строки для записи в файл
     * @return mixed
     */
    abstract function writeLine($data);

    /**
     * Читает одну строку данных из файла
     *
     * @return array
     */
    abstract function readLine();

    /**
     * Возвращает на какой последней позиции все остановилось
     *
     * @return integer
     */
    abstract function tellPosition();

    /**
     * Перемещает указатель на нужную позицию в файле
     *
     * @param integer $position позиция, которая была возвращена через tellPosition()
     * @return mixed
     */
    abstract function seekPosition($position);

    /**
     * Возвращает расширение файла без точки, которое имеет данный формат
     *
     * @return string
     */
    abstract function getFileExtension();

    /**
     * Возвращает строковый идентификатор формата данных
     *
     * @return string
     */
    abstract static function getId();

    /**
     * Возвращает название формата данных
     *
     * @return string
     */
    abstract static function getTitle();

    /**
     * Устанавливает произвольные параметры для импорта/экспорта
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Устанавливает произвольный параметр для импорта/экспорта
     *
     * @param string $key Ключ параметра
     * @param string $value Значение параметра
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Возвращает произвольный параметр для импорта/экспорта
     *
     * @param string $key Ключ параметра
     * @param null $default Значение параметра по умолчанию
     * @return mixed|null
     */
    function getOption($key, $default = null)
    {
        return $this->options[$key] ?? $default;
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
        //Ничего не конвертируем, так как PHPSpreadsheet всегда ожидает данные в UTF-8
        return $string_in_utf8;
    }

    /**
     * Данный метод должен вернуть строку всегда в UTF-8 формате.
     * В случае с CSV, данные должны быть переконвертированы из тех, что выбранны в настройках в UTF-8
     *
     * @param string $string Строка данных
     * @param string $csv_charset Ожидаемая кодировка, выбранная для CSV в настройках системного модуля
     * @return false|string
     */
    function prepareImportCellDataCharset($string, $csv_charset)
    {
        return $string;
    }
}