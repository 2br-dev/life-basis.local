<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace antivirus\model;

use Antivirus\Config\File;
use RS\Config\Loader;
use RS\Http\Request;
use RS\Module\Manager;

/**
 * Класс содержит общие методы, используемые в разных компонентах антивируса
 *
 * Class Utils
 * @package antivirus\model
 */
class Utils
{
    /**
     * Выводить ли лог на стандартный вывод (делать echo)
     *
     * @var bool
     */
    static public $echo_log = true;

    /**
     * @var Manager
     */
    static private $module_manager;

    /**
     * Кеширующий список подписанных файлов, разбитый по модулям.
     * Имя модуля является ключом массива, массив путей файлов - значением
     *
     * @var array
     */
    static private $modules_signed_files = [];

    static function getModuleManager()
    {
        if(self::$module_manager == null)
            self::$module_manager = new Manager();
        return self::$module_manager;
    }

    /**
     * Логгирование в файл /storage/logs/antivirus.log
     *
     * @param $msg
     */
    static function log($msg)
    {
        if(self::$echo_log)
        {
            echo "{$msg}\n";
            flush();
        }
    }

    static function logNewLine($count = 1)
    {
        if(self::$echo_log)
        {
            echo str_repeat("\n", $count);
            flush();
        }
    }

    /**
     * Возвращает список системных (входящих в комплектацию) модулей.
     * Таже возвращает "виртуальный" модуль core в начале списка.
     *
     * @return \RS\Module\Item []
     */
    static function getSystemModules()
    {
        $module_manager     = self::getModuleManager();
        $system_modules     = [];
        $system_modules[]   = new FakeCoreModuleItem();

        foreach($module_manager->getList() as $module_item)
        {
            /**
             * @var $module_item \RS\Module\Item
             */
            if($module_item->getConfig()->is_system)
            {
                $system_modules[] = $module_item;
            }
        }

        return $system_modules;
    }



    /**
     * Возвращает относительный путь к файлу с подписями для данного модуля.
     * Некоторые модули имеют специфический путь к этому файлу
     * в частности "виртуальный" модуль Core
     *
     * @param \RS\Module\Item $module
     * @return string
     */
    static function getSignaturesFilePath($module)
    {
        if($module instanceof FakeCoreModuleItem)
        {
            return 'core/rs/config/signatures.xml';
        }
        else
        {
            return 'config/signatures.xml';
        }
    }


    /**
     * Количество файлов в папке
     * @param $path
     * @param bool $use_cache
     * @return int
     */
    static function getFilesCount($path, $use_cache = false)
    {
        if($use_cache)
        {
            return \RS\Cache\Manager::obj()->request(['\Antivirus\Model\Utils', 'getFilesCount'], $path, false);
        }

        $recIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS);
        $objects = new \RecursiveIteratorIterator($recIterator);
        return iterator_count($objects);
    }


    /**
     * Возвращает количество подписанных файлов модуля
     * Основывается на количестве записей в файле signatures.xml
     *
     * @param \RS\Module\Item|FakeCoreModuleItem $module
     * @return int
     */
    static function getSignedFilesCount($module)
    {
        $signatures_xml_file = $module->getFolder() . '/' . self::getSignaturesFilePath($module);
        try
        {
            $simpleXml = new \SimpleXMLElement(@file_get_contents($signatures_xml_file));
            return count($simpleXml->file);
        }
        catch(\Exception $e)
        {
            return 1; // 1 файл - это сам signatures.xml
        }
    }

    /**
     * Возвращает путь к файлу относительно корня сайта
     *
     * @param string $file_module_relative_path путь к файлу относительно корня модуля
     * @param \RS\Module\Item $module
     * @return string
     */
    static function toRelativePath($file_module_relative_path, $module)
    {
        if($module instanceof FakeCoreModuleItem)
        {
            $path = $file_module_relative_path;
        }
        else
        {
            $path = \Setup::$MODULE_FOLDER . '/' . $module->getName() . '/' . $file_module_relative_path;
        }
        return ltrim($path, '/');
    }

    /**
     * Возвращает путь к файлу относительно корня сайта
     *
     * @param string $full_path полный путь к файлу
     * @return string
     */
    static function fullPathToRelative($full_path)
    {
        $path = self::replaceFirst(\Setup::$PATH, '', $full_path);
        return ltrim($path, '/');
    }


    /**
     * Заменяет первое вхождение строки $search на $replace в строке $subject
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string результат замены
     */
    static function replaceFirst($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false)
        {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }


    /**
     * Является ли файл "подписанным".
     * То есть присутствует ли файл в одном из signatures.xml
     *
     * @param string $rel_path путь к файлу относительно корня сайта (без ведущего слеша)
     * @return bool
     */
    static function isFileSigned($rel_path)
    {
        $module = self::getModuleByFile($rel_path);
        if(!$module) return false;

        $module_name = $module->getName();

        if(!isset(self::$modules_signed_files[$module_name]))
        {
            try
            {
                self::$modules_signed_files[$module_name] = self::getModuleSignedFiles($module);
            }
            catch(\Exception $e)
            {
                return false;
            }
        }

        $signed_files = self::$modules_signed_files[$module_name];

        return array_search($rel_path, $signed_files) !== false;
    }

    /**
     * Является ли файл хранилищем подписей (signatures.xml)
     *
     * @param $rel_path
     * @return bool
     */
    static public function isFileSignaturesDatabase($rel_path)
    {
        return basename($rel_path) == 'signatures.xml';
    }

    /**
     * Возвращает объект модуля, которому пренадлежит данный файл
     *
     * @param $rel_path
     * @return \RS\Module\Item|FakeCoreModuleItem|null
     */
    static function getModuleByFile($rel_path)
    {
        if(self::isCoreModuleFile($rel_path))
        {
            return new FakeCoreModuleItem();
        }
        else
        {
            if(strpos($rel_path, 'modules/') === 0)
            {
                $path_parts = explode('/', $rel_path);
                $module_name = $path_parts[1];
                $list = self::getModuleManager()->getList();
                return isset($list[$module_name]) ? $list[$module_name] : null;
            }
        }
        return null;
    }


    /**
     * Возвращает версию модуля в текущей системе
     *
     * @param \RS\Module\Item|FakeCoreModuleItem $module
     * @return string
     */
    static function getModuleVersion($module)
    {
        if($module instanceof FakeCoreModuleItem)
        {
            return \Setup::$VERSION;
        }

        if($module instanceof \RS\Module\Item)
        {
            $cfg = $module->getConfig();
            return $cfg['version'];
        }

        return null;
    }


    /**
     * Принадлежит ли файл виртуальному модулю core
     *
     * @param $rel_path
     * @return bool
     */
    static function isCoreModuleFile($rel_path)
    {
        if(strpos($rel_path, 'core/') === 0) return true;
        if(strpos($rel_path, 'resource/') === 0) return true;
        if(strpos($rel_path, 'templates/system/') === 0) return true;
        return false;
    }

    /**
     * Является ли текущий запрос доверенным.
     * Возвращает true, если IP-адрес или UserAgent находятся в списке доверенных.
     * Список доверенных настраивается в настройках модуля Antivirus
     *
     * @param File $config
     * @return bool
     */
    static function isRequestTrusted(File $config = null)
    {
        if($config === null)
        {
            $config = Loader::byModule(__CLASS__);
        }
        $server_array   = Request::commonInstance()->getSource(SERVER);

        $trusted_ips    = trim((string)$config->proactive_trusted_ips);
        $trusted_agents = trim((string)$config->proactive_trusted_user_agents);
        $trusted_urls   = trim((string)$config->proactive_trusted_url);

        $trusted_ips    = $trusted_ips !== "" ? preg_split("/\r?\n/", $trusted_ips, -1, PREG_SPLIT_NO_EMPTY) : [];
        $trusted_agents = $trusted_agents !== "" ? preg_split("/\r?\n/", $trusted_agents, -1, PREG_SPLIT_NO_EMPTY) : [];
        $trusted_urls   = $trusted_urls !== "" ? preg_split("/\r?\n/", $trusted_urls, -1, PREG_SPLIT_NO_EMPTY) : [];

        if(in_array($server_array['REMOTE_ADDR'], $trusted_ips))
        {
            return true;
        }

        if ($trusted_agents && isset($server_array['HTTP_USER_AGENT'])) {
            foreach ($trusted_agents as $pattern) {
                if (preg_match("#{$pattern}#i", $server_array['HTTP_USER_AGENT'])) {
                    return true;
                }
            }
        }

        if ($trusted_urls && isset($server_array['REQUEST_URI'])) {
            foreach ($trusted_urls as $pattern) {
                if (mb_stripos($server_array['REQUEST_URI'], trim($pattern)) !== false) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Загрузить список подписанных файлов модуля
     *
     * @param \RS\Module\Item|FakeCoreModuleItem $module
     * @return string[]
     */
    private static function getModuleSignedFiles($module)
    {
        $sig_path = $module->getFolder() . '/' . self::getSignaturesFilePath($module);

        if(!file_exists($sig_path))
        {
            throw new \Exception(t('Не найден signatures.xml в модуле \'%0\'', [$module->getName()]));
        }

        $simpleXml = new \SimpleXMLElement(file_get_contents($sig_path));
        $ret = [];
        foreach($simpleXml->file as $one)
            $ret[] = Utils::fullPathToRelative($module->getFolder() . '/' . $one->path);
        return $ret;
    }
}