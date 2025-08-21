<?php
require(__DIR__.'/core/rs/config/abstractsetup.inc.php');

/**
* Класс с описанием системных настроек. 
* Данный файл НЕ изменяется при обновлении системы
*/
class Setup extends \RS\Config\AbstractSetup
{
    /**
    * Инициализирует настройки проекта
    */
    public static function init()
    {
        /* Здесь можно переназначить стандартные значения свойств */ 
        \Setup::$DETAILED_EXCEPTION=true;
        \Setup::$WRITE_EXCEPTIONS_TO_FILE = true;
        parent::init();
    }
}

\Setup::init();