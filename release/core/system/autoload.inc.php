<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

/**
* Класс создан для установки дополнительных параметров функции __autoload
*/
class Autoload 
{
    private static $require_path;
    private static $class_map_modified = false;
    private static $class_map = [];
    private static $cache_file = CACHE_FOLDER.'/autoload-map.inc.php';
    
    function __construct()
    {
        self::restoreDefaultPath(); //Инициализируем стандартные пути к автозагрузке.
        self::loadClassMap(); //Инициируем кэш готовых путей к файлам с классами
        spl_autoload_register([$this, 'autoload']);
    }

    /*
    * Автозагрузка классов.
    * Если класс начинается с последовательности RS\, значит это класс ядра, иначе - это класс модуля
    * Класс ищется в соответствующей пространству имен папке с расширениями: .my.inc.php и .inc.php 
    * Поддержка .my.inc.php осуществляется для возможности кастомизации системных классов.
    * Классы с расширениями .my.inc.php - не изменяются при обновлении системы.
    * При использовании кастомных классов и регулярном обновлении системы, стабильность системы не гарантируется,
    * т.к. кастомные классы могут утратить актуальность и не соответствовать обновленному состоянию системы
    * 
    * @return void
    */
    function autoload($class_name) 
    {
        $class_name = strtolower(str_replace(['\\', '.'], ['/', ''], $class_name));

        if (isset(self::$class_map[$class_name])) {
            if (include(self::$class_map[$class_name])) {
                return;
            }
        }

        $require_path = self::getPath();

        if ($class_name[0] == 'r' && $class_name[1] == 's' && $class_name[2] == '/') {
            //Классы ядра
            $class_path = $require_path['systemClass'] . $class_name;
        } else {
            //Классы модулей
            $class_path = $require_path['moduleClass'] . $class_name;
        }

        $custom_class = $class_path . '.' . \Setup::$CUSTOM_CLASS_EXT;
        $class = $class_path . '.' . \Setup::$CLASS_EXT;

        if (file_exists($custom_class)) {
            $final_path = $custom_class;
        } elseif (file_exists($class)) {
            $final_path = $class;
        } else {
            $final_path = false;
        }

        if ($final_path) {
            require($final_path);
            self::$class_map[$class_name] = $final_path;
            self::$class_map_modified = true;
        }
    }
    
    /**
    * Устанавливает пути к каталогам с классами. Удобно применять если нужно искать классы во временных папках.
    * 
    * @param string $moduleClass путь к классам модулей
    * @param string $systemClass путь к системным классам
    * @return void
    */
    public static function setPath($moduleClass, $systemClass = null)
    {
        if (isset($moduleClass)) self::$require_path['moduleClass'] = $moduleClass;
        if (isset($systemClass)) self::$require_path['systemClass'] = $systemClass;
    }
    
    /**
    * Возвращает массив текущих путей для поиска классов
    * 
    * @return array
    */
    public static function getPath() 
    {
        return self::$require_path;
    }    
    
    /**
    * Устанавливает пути к классам, определенные по умолчанию
    * 
    * @return void
    */
    public static function restoreDefaultPath()
    {
        self::$require_path = [
                'systemClass' => \Setup::$PATH."/core/",
                'moduleClass' => \Setup::$PATH."/modules/"
        ];
    }

    /**
     * Загружает кэш файл с данными о связи имени класса с файлом
     *
     * @return void
     */
    protected static function loadClassMap()
    {
        if (file_exists(self::$cache_file)) {
            try {
                self::$class_map = (array)require(self::$cache_file);
            } catch (Throwable $e) {}
        }
    }

    /**
     * Сохраняет кэш файл с данными о связи имени класса с файлом
     *
     * @return void
     */
    protected static function saveClassMap()
    {
        if (self::$class_map_modified) {
            if (!is_dir(\Setup::$CACHE_FOLDER)) {
                umask(0);
                mkdir(\Setup::$CACHE_FOLDER, \Setup::$CREATE_DIR_RIGHTS, true);
            }
            file_put_contents(self::$cache_file, '<?php return '.var_export(self::$class_map, true).';');
        }
    }

    /**
     * Очищает локальный кэш связей имен классов с файлами
     *
     * @return void
     */
    public static function cleanClassMap()
    {
        self::$class_map = [];
        self::$class_map_modified = true;
    }

    /**
     * Обработчик выгрузки класса из памяти
     */
    public function __destruct()
    {
        self::saveClassMap();
    }
}

new Autoload();
