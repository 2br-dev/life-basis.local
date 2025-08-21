<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail\MsTnef;

class FileBase
{
   public $name;
   public $name_is_unicode = FALSE;
   public $code_page = '';
   public $message_code_page = '';
   public $type;
   public $content;
   public $created;
   public $modified;
 
   function __construct()
   {
      $this->name = 'Untitled';
      $this->type = 'application/octet-stream';
      $this->content = '';
   }

   function setMessageCodePage($code_page)
   {
      $this->message_code_page = $code_page;
   }

   function getCodePage()
   {
      if (empty($this->code_page))
         return $this->message_code_page;
      else
         return $this->code_page;
   }

   function getName()
   {
       if ($this->name_is_unicode) {
           return substr(mb_convert_encoding($this->name, "UTF-8" , "UTF-16LE"), 0, -1);
       }
       return $this->name;
   }

   function getType()
   {
       return $this->type;
   }

   function getSize()
   {
      return strlen($this->content);
   }

   function getCreated()
   {
      return $this->created;
   }

   function getModified()
   {
      return $this->modified;
   }

   function getContent()
   {
      return $this->content;
   }

}