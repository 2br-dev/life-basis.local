<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

use ExternalApi\Config\File;

class ResultFormatter
{
    /**
    * Подготавливает данные в заданном формате
    * 
    * @param array $data - произвольные данные
    * @param string $format - формат json или xml
    */
    public static function format($data, $format = 'json')
    {
        switch($format) {
            default:
                $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
                if (File::config()->pretty_print_response) {
                    $flags |= JSON_PRETTY_PRINT;
                }
                $result = json_encode($data, $flags);
        }
        return $result;
    }    
}
