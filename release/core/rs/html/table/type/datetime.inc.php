<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

class Datetime extends Text
{
    public 
        $property = [
            'format' => 'd.m.Y H:i'
    ];
    
    function getValue()
    {
        $time = is_numeric($this->value) ? $this->value : strtotime((string)$this->value);
        return $this->value > 0 ? date($this->property['format'], $time) : t('нет');
    }
}

