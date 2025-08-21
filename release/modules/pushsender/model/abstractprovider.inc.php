<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model;

use PushSender\Model\Log\PushSenderLog;

/**
* Базовый класс для провайдеров Push уведомлений.
* Провайдером счиается класс, который организует запрос к внешним API 
* и передает конкретный тип Push уведомлений
*/
abstract class AbstractProvider
{
    protected
        $log;
        
    function __construct()
    {
        $this->log = PushSenderLog::getInstance();
    }
    
    /**
    * Добавляет одну строку в log файл
    * 
    * @param string $line
    * @return AbstractProvider
    */
    protected function writeLog($line)
    {
        $this->log->write($line);
        return $this;
    }    
    
    /**
    * Отправляет уведомление 
    * 
    * @param \PushSender\Model\AbstractPushNotice $push - Уведомление
    * @return boolean Возвращает true, в случае выполнения запроса на отправку, в противном случае - false
    */
    abstract function transfer($push);
    
    /**
    * Возвращает реузльтат, который вернуло удаленное API
    * 
    * @return mixed
    */
    abstract function getResponse();
    
    /**
    * Возвращает ошибку, которая возникла до отправки уведомления
    * 
    * @return string
    */
    abstract function getError();
}