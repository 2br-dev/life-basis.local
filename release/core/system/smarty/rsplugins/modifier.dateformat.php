<?php

use RS\Helper\Tools as HelperTools;

/**
* Расширенный плагин форматирования даты
*/
function smarty_modifier_dateformat($string, $format = "@date", $default_date = '')
{

    /**
    * Include the {@link shared.make_timestamp.php} plugin
    */
    require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');
    
    if ($string != '') {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    }

    $format = HelperTools::dateExtend($format, $timestamp);
    return date($format, $timestamp);
} 

