<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Exchange\Model\Task\Sale;

use Exchange\Model\Task\AbstractTask;

/**
 * Объект этого класса хранится в сессии, соотвественно все свойства объекта доступны
 * не только до окончания выполнения скрипта, но и в течение всей сессии
 */
class ImportDocument extends AbstractTask
{
    protected $filename;
    protected $offset = 0;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function exec($max_exec_time = 0)
    {
        $api = \Exchange\Model\Api::getInstance();
        $readed_nodes = $api->saleImport($this->filename, $this->offset, $max_exec_time);

        if ($readed_nodes === true) {
            return true;
        } else {
            $this->offset = $readed_nodes + 1;
        }
    }

    public function getOffset()
    {
        return $this->offset;
    }
}