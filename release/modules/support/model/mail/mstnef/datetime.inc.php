<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail\MsTnef;

class Datetime
{
   public $year;
   public $month;
   public $day;
   public $hour;
   public $minute;
   public $second;

   function setTnefBuffer($buffer)
   {
      $this->year = tnef_geti16($buffer);
      $this->month = tnef_geti16($buffer);
      $this->day = tnef_geti16($buffer);
      $this->hour = tnef_geti16($buffer);
      $this->minute = tnef_geti16($buffer);
      $this->second = tnef_geti16($buffer);
   }

   function getString()
   {
      return sprintf("%04d-%02d-%02d %02d:%02d:%02d",
                     $this->year, $this->month, $this->day,
                     $this->hour, $this->minute, $this->second);
   }
}