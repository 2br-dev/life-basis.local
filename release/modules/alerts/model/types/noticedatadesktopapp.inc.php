<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\Types;

/**
* Объект с обязательными параметрами для уведомления
*/
class NoticeDataDesktopApp
{
    /**
    * Заголовок уведомления
    *
    * @var string
    */
    public $title;
        
    /**
    * Краткий текст уведомления без тегов
    *
    * @var string
    */
    public $short_message;
        
    /**
    * Абсолютная ссылка для перехода на сайт
    *
    * @var string
    */
    public $link;
        
    /**
    * Подпись к ссылке
    *
    * @var string
    */
    public $link_title;
        
    /**
    * Переменные, которые будут переданы в шаблон уведомления
    *
    * @var array
    */
    public $vars;
        
    /**
    * ID одного или нескольких пользователей, для которых предназначено сообщение.
    * Если не заполнено или 0, то предназначается всем пользователям Desktop приложения.
    *
    * @var integer|array
    */
    public $destination_user_id;

}
