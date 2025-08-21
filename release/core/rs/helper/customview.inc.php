<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Helper;

/**
* В данном классе будут собраны функции, приводящие данные к виду, который может различаться в зависимости от настроек.
*/
class CustomView
{
    /**
    * Форматирование цены.
    * Например 7230.2 будет выглядеть - 7 230.20 руб.
    * 
    * @param double $num
    * @param string $currency_liter - валюта
    * 
    * @return string
    */
    public static function cost($money, $currency_liter = null)
    {
        $dec_point = '.';
        $thousands_sep = ' ';
        $format = '{cost} {currency}';
        
        $dec = ((int)$money == $money) ? 0 : 2; //Если у цены нет дробной части, не отображаем ее
        $formatted = (float)sprintf("%01.2f", $money);
        $cost = number_format($formatted, $dec, $dec_point, $thousands_sep);
        
        return trim(str_replace(['{cost}', '{currency}'], [$cost, $currency_liter], $format));
    }

    /**
     * Возвращает дату и время в формате d.m.Y H:i
     *
     * @param integer|string $time Дата в формате timestamp или Y-m-d H:i:s
     * @param bool $with_second
     * @return string
     */
    public static function dateTime($time, $with_second = true)
    {
        $timestamp = is_numeric($time) ? $time : strtotime($time);
        return date('d.m.Y H:i'.($with_second ? ':s' : ''), $timestamp);
    }

    /**
     * Возвращает дату
     *
     * @param integer|string $time Дата в формате timestamp или Y-m-d H:i:s
     * @return string
     */
    public static function date($time)
    {
        $timestamp = is_numeric($time) ? $time : strtotime($time);
        return date('d.m.Y', $timestamp);
    }
}

