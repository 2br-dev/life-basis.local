<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Format\Type;

class Xlsx extends Xls
{
    static $file_extension = 'xlsx';

    /**
     * Возвращает объект класса чтения данных в текущем формате
     *
     * @return \PhpOffice\PhpSpreadsheet\Reader\Xlsx
     */
    protected function initReader()
    {
        return new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    }

    /**
     * Возвращает объект класса записи данных в текущем формате
     *
     * @return \PhpOffice\PhpSpreadsheet\Writer\Xlsx
     */
    protected function initWriter()
    {
        return new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);
    }
}