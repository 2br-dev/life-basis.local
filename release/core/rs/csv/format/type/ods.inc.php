<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Format\Type;

class Ods extends Xls
{
    static $file_extension = 'ods';

    /**
     * Возвращает объект класса чтения данных в текущем формате
     *
     * @return \PhpOffice\PhpSpreadsheet\Reader\Ods
     */
    protected function initReader()
    {
        return new \PhpOffice\PhpSpreadsheet\Reader\Ods();
    }

    /**
     * Возвращает объект класса записи данных в текущем формате
     *
     * @return \PhpOffice\PhpSpreadsheet\Writer\Ods
     */
    protected function initWriter()
    {
        return new \PhpOffice\PhpSpreadsheet\Writer\Ods($this->spreadsheet);
    }
}