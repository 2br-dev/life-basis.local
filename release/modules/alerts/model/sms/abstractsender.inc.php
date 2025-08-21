<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Model\SMS;

use RS\Exception;

abstract class AbstractSender
{
    private $extra_data;

    /**
    * Возвращает сокращенное название провайдера (только латинские буквы)
    * @return string
    */
    abstract public function getShortName();
    
    /**
    * Возвращает отображаемое название провайдера
    * @return string
    */
    abstract public function getTitle();

    /**
    * Отправка SMS
    *
    * @param string $text
    * @param array $phone_numbers
    * @return bool
    * @throws Exception Бросает исключение в случае ошибки
    */
    abstract public function send($text, $phone_numbers);

    /**
     * Устанавливает произвольные данные, которые могут быть использованы при отправке сообщения
     *
     * @param $data
     */
    public function setExtraData($data)
    {
        $this->extra_data = $data;
    }

    /**
     * Возвращает произвольные данные, установленные через метод setExtraData
     *
     * @return mixed
     */
    public function getExtraData()
    {
        return $this->extra_data;
    }
}