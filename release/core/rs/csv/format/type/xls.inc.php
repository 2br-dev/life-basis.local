<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Format\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RS\Csv\Format\AbstractFormatType;

/**
 * Класс формата экспорта данных - XLS
 * Объект класса должен использоваться либо для записи данных, либо для чтения
 */
class Xls extends AbstractFormatType
{
    protected $filename;
    protected static $file_extension = 'xls';
    /**
     * @var $worksheet Worksheet
     */
    protected $worksheet;
    protected $spreadsheet;
    protected $line = 0;
    protected $reader;
    protected $writer;
    protected $data = [];

    /**
     * Открывает файл для чтения или записи.
     *
     * @param string $filename полный путь к файлу
     * @param string $mode не используется
     * @return bool Возвращает true, если файл может быть успешно открыт
     */
    function openFile($filename, $mode)
    {
        $this->filename = $filename;

        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();

        return file_exists($this->filename);
    }

    /**
     * Закрывает файл
     *
     * @return bool
     */
    function closeFile()
    {
        if (!$this->reader && $this->writer) {
            $this->writer->save($this->filename);
        }
        return true;
    }

    /**
     * Записывает одну строку данных в файл
     *
     * @param array $data Данные для одной строки для записи в файл
     * @return mixed
     */
    function writeLine($data)
    {
        if (!$this->writer) {
            $this->writer = $this->initWriter();
        }
        $this->worksheet->fromArray([$data], null, 'A'.($this->line + 1));
        $this->line++;
    }

    /**
     * Читает одну строку данных из файла
     *
     * @return array
     */
    function readLine()
    {
        if (!$this->reader) {
            $this->reader = $this->initReader();
            $this->reader->setReadDataOnly(true);
            $spreadsheet = $this->reader->load($this->filename);
            $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
            $this->data = $sheet->toArray();
        }

        if (isset($this->data[$this->line])) {
            $result = $this->data[$this->line];
            $this->line++;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Возвращает объект класса чтения данных в текущем формате
     *
     * @return \PhpOffice\PhpSpreadsheet\Reader\Xls
     */
    protected function initReader()
    {
        return new \PhpOffice\PhpSpreadsheet\Reader\Xls();
    }

    /**
     * Возвращает объект класса записи данных в текущем формате
     *
     * @return \PhpOffice\PhpSpreadsheet\Writer\Xls
     */
    protected function initWriter()
    {
        return new \PhpOffice\PhpSpreadsheet\Writer\Xls($this->spreadsheet);
    }

    /**
     * Сбрасывает указатель чтения данных
     */
    protected function resetReader()
    {
        $this->reader = null;
        $this->data = [];
        $this->seekPosition(0);
    }

    /**
     * Возвращает на какой последней позиции все остановилось
     *
     * @return integer
     */
    function tellPosition()
    {
        return $this->line;
    }

    /**
     * Перемещает указатель на нужную позицию в файле
     *
     * @param integer $position позиция, которая была возвращена через tellPosition()
     * @return mixed
     */
    function seekPosition($position)
    {
        $this->line = $position;
    }

    /**
     * Возвращает расширение файла без точки, которое имеет данный формат
     *
     * @return string
     */
    function getFileExtension()
    {
        return static::$file_extension;
    }

    /**
     * Возвращает строковый идентификатор формата данных
     *
     * @return string
     */
    static function getId()
    {
        return static::$file_extension;
    }

    /**
     * Возвращает название формата данных
     *
     * @return string
     */
    static function getTitle()
    {
        return strtoupper(static::$file_extension);
    }
}