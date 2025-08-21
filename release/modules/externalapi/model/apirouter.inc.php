<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;
use ExternalApi\Config\File;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Orm\VirtualApp;
use RS\Cache\Manager as CacheManager;
use RS\Event\Manager as EventManager;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Request;
use RS\Site\Manager as SiteManager;

class ApiRouter
{
    const CONTEXT_TYPE_CONFIG = 'config';
    const CONTEXT_TYPE_VIRTUAL = 'virtual';

    public static $api_method_folder = '/model/externalapi';
    private static $instance;

    private $context_type = self::CONTEXT_TYPE_CONFIG;
    private $context_virtual_app;
    private $version;
    private $lang;
        
    function __construct($version, $lang)
    {
        $this->version = $version;
        $this->lang = $lang;
    }

    /**
     * Возвращает один "общий" экземпляр текущего класса
     *
     * @return static
     */
    public static function getInstance($version = AbstractMethod::BASE_VERSION,
                                       $lang = AbstractMethod::DEFAULT_LANGUAGE)
    {
        return self::$instance ?? (self::$instance = new self($version, $lang));
    }

    /**
     * Устанавливает контекст доступа к методу API
     *
     * @param string $type Тип контекста. Бывает стандартный (self::CONTEXT_TYPE_CONFIG) и виртуальный (self::CONTEXT_TYPE_VIRTUAL)
     * @param VirtualApp|null $virtual_app Объект виртуального приложения. Актуально, если тип контекста - виртуальный
     *
     * @return void
     * @throws ApiException Бросает исключение, если установлен виртуальный тип и не передан объект виртуального приложения
     */
    public function setContext($type, VirtualApp $virtual_app = null)
    {
        if ($type == self::CONTEXT_TYPE_VIRTUAL && !$virtual_app) {
            throw new ApiException(t('Необходимо передать объект виртуального приложения для контекста virtual'));
        }

        $this->context_type = $type;
        $this->context_virtual_app = $virtual_app;
    }

    /**
     * Устанавливает контекст выполнения API по ключу, пришедшему в URL
     *
     * @param string $api_key
     * @return void
     */
    public function setContextByApiKey($api_key)
    {
        $config = File::config();
        if (mb_strtolower((string)$config['api_key']) == mb_strtolower((string)$api_key)) {
            $this->setContext(self::CONTEXT_TYPE_CONFIG);
        } else {
            $virtual_apps = Request::make()
                ->from(new VirtualApp())
                ->where([
                    'use_vapp_endpoint' => 1
                ])->objects(null, 'vapp_endpoint_api_key');

            if (isset($virtual_apps[$api_key])) {
                $this->setContext(self::CONTEXT_TYPE_VIRTUAL, $virtual_apps[$api_key]);
            } else {
                throw new ApiException(t('Неверный API-ключ'));
            }
        }
    }

    /**
     * Возвращает контекст доступа - это стандартный доступ по API-ключу из настроек модуля
     * или доступ по API-ключу виртуального приложения
     *
     * @return string
     */
    public function getContextType()
    {
        return $this->context_type;
    }

    /**
     * Возвращает виртуальное приложение, которое связано с текущим контекстом запуска
     *
     * @return VirtualApp|null
     */
    public function getContextVirtualApp()
    {
        return $this->context_virtual_app;
    }
    
    /**
    * Выполняет один метод API
    * 
    * @param string $method
    * @param array $http_request
    */
    public function runMethod($method, $params)
    {
        $method_instance = $this->getMethodInstance($method);
        
        if ($method_instance) {
            if ($this->getContextType() == self::CONTEXT_TYPE_VIRTUAL) {
                $method_instance->setContextVirtualApp($this->getContextVirtualApp());
            }
            $result = $method_instance->run($params, $this->version, $this->lang);

            $event_result = EventManager::fire('apirouter.aftermethodrun', [
                'result' => $result,
                'method' => $method_instance,
                'version' => $this->version,
                'lang' => $this->lang,
            ]);
            return $event_result->getResult()['result'];
        } else {
            throw new ApiException(t('Метод API не найден'), ApiException::ERROR_METHOD_NOT_FOUND);
        }
    }
    
    /**
    * Возвращает POST И GET параметры, которые будут переданы в API метод
    * 
    * @param string $method 
    * @param \RS\Http\Request $http_request
    * @return array
    */
    public function makeParams($method, \RS\Http\Request $http_request)
    {
        $params = [];
        
        if ($method_instance = $this->getMethodInstance($method)) {
            $allow_request_methods = $method_instance->getAcceptRequestMethod();

            if (in_array(JSON, $allow_request_methods)) {
                $data = file_get_contents('php://input');
                $params = json_decode($data, true);
            }

            if (in_array(POST, $allow_request_methods)) {
                foreach($http_request->getSource(POST) as $key => $value) {
                    $params[$key] = $http_request->post($key, is_array($value) ? TYPE_ARRAY : TYPE_STRING);
                }
            }

            if (in_array(GET, $allow_request_methods)) {
                foreach($http_request->getSource(GET) as $key => $value) {
                    $params[$key] = $http_request->get($key, is_array($value) ? TYPE_ARRAY : TYPE_STRING);
                }
            }
            
            if (in_array(FILES, $allow_request_methods)) {
                foreach($http_request->getSource(FILES) as $key => $value) {
                    $params[$key] = $http_request->files($key);
                }
            }
        }
        return $params;
    }
    
    /**
    * Возвращает все возможные версии методов API, от самой нижней до верхней
    * 
    * @param mixed $cache
    */
    public static function getMethodsVersions($cache = true)
    {
        if ($cache) {
            return CacheManager::obj()
                ->request([__CLASS__, __FUNCTION__], false);
        } else {
            $versions = [];
            foreach(self::getGroupedMethodsInfo() as $methods) {
                foreach($methods as $method) {
                    foreach($method['versions'] as $version) {
                        $versions[$version] = $version;
                    }
                }
            }
            natsort($versions);
            return $versions;            
        }
    }
    
    
    /**
    * Возвращает все существующие языки, для которых есть описания API
    * 
    * @param bool $cache
    * @return array
    */
    public function getMethodsLanguages($cache = true)
    {
        if ($cache) {
            return CacheManager::obj()
                ->request([$this, __FUNCTION__], false);
                
        } else {
            $languages = [AbstractMethod::DEFAULT_LANGUAGE => 1];
            
            foreach($this->getGroupedMethodsInfo() as $methods) {
                foreach($methods as $method) {
                    $languages += self::findLanguages($method['comment']);
                    $languages += self::findLanguages($method['example']);
                    $languages += self::findLanguages($method['return']);
                    
                    foreach($method['params'] as $param_data) {
                        $languages += self::findLanguages($param_data['comment']);
                    }
                }
            }
            return array_keys($languages);
        }
    }
    
    
    /**
    * Ищет языки, используемые в комментариях к методам
    * 
    * @param string $text
    * @return array
    */
    private static function findLanguages($text)
    {
        if (preg_match_all('/\#lang-(\w+)\:/', (string)$text, $match)) {
            return array_flip(array_map('strtolower', $match[1]));
        }
        return [];
    }
    
    /**
    * Возвращает информацию о всех методах API, присутствующих в системе
    * 
    * @param string $lang - двухсимвольный идентификатор
    * @param bool $cache - использовать кэширование
    * 
    * @return array
    */
    public function getGroupedMethodsInfo($lang = null, $cache = true)
    {
        $result = [];
        foreach($this->getApiMethods($cache) as $method) {
            $method_instance = new $method['class']();
            $info = $method_instance->getInfo($lang);
            
            $versions = [];
            foreach($info as $version => $method_info) {
                $result[$method['method_group']][$method['method']] = $method_info;
                $versions[] = $version;
            }
            
            $result[$method['method_group']][$method['method']]['versions'] = $versions;
        }
        
        return $result;
    }
    
    /**
    * Возвращает инстанс класса, который обрабатывает метод API
    * 
    * @param string $method - Имя метода, например oauth.authorize
    * @param bool $only_allowable - Если true, то будут возвращены инстансы только включенных в настройках модуля методов
    * @return \ExternalApi\Model\AbstractMethod|false
    */
    public function getMethodInstance($method, $only_allowable = false)
    {
        $method = strtolower($method);
        $methods_list = $this->getApiMethods(true, $only_allowable);
        
        if (isset($methods_list[$method])) {
            return new $methods_list[$method]['class']();
        }
        return false;
    }
    
    /**
    * Возвращает список методов, имеющихся в системе для отображения в элементе select
    * @return [
    *     'id метода' => '(id метода) описание метода',
    *     'id метода' => '(id метода) описание метода',
    *     ....  
    * ]
    */
    public static function getApiMethodsSelectList(array $root_item = [], $only_allowable = false, $lang = null, $cache = true)
    {
        $result = [];
        foreach(self::getInstance()->getApiMethods($cache, $only_allowable) as $method) {
            $method_instance = new $method['class']();
            $info = $method_instance->getInfo($lang);
            
            $description = '';
            foreach($info as $version => $method_info) {
                $description = $method_info['method']." <i><small>({$method_info['comment']})</small></i>";
            }
            
            $result[strtolower($method['method'])] = $description;
        }
        
        return $root_item + $result;
    }

    /**
     * Возвращает список методов, имеющихся в системе для отображения в элементе select
     *
     * @return array
     */
    public static function staticSelectList(array $first = [], $only_allowable = false, $cache = true)
    {
        $result = [];
        foreach(self::getInstance()->getApiMethods($cache, $only_allowable) as $method) {
            $method_instance = new $method['class']();
            $info = $method_instance->getInfo();

            $description = '';
            foreach($info as $version => $method_info) {
                $description = $method_info['method'];
            }

            $result[strtolower($method['method'])] = $description;
        }

        return $first + $result;
    }

    /**
     * Возвращает список методов, поддерживающих авторизационный токен, имеющихся в системе для отображения в элементе select
     *
     * @param array $root_item
     * @param bool $only_allowable - Если true, то будут возвращены только разрешенные в настройках модуля методы
     * @param null $lang - Язык для справки
     * @param bool $cache - Если true, то будет использоваться кэширование
     */
    public static function getAuthorizedApiMethodsSelectList(array $root_item = [], $only_allowable = false, $lang = null, $cache = true)
    {
        $result = [];
        foreach(self::getInstance()->getApiMethods($cache, $only_allowable) as $method) {
            $method_instance = new $method['class']();
            if ($method_instance instanceof AbstractAuthorizedMethod) {
                $info = $method_instance->getInfo($lang);

                $description = '';
                foreach ($info as $version => $method_info) {
                    $description = $method_info['method'] . " <i><small>({$method_info['comment']})</small></i>";
                }

                $result[strtolower($method['method'])] = $description;
            }
        }

        return $root_item + $result;
    }
    
    /**
    * Возвращает полный список методов API, которые существуют во всех включенных модулях текущего сайта.
    * Классы с обработчиками методов должны находиться в папке /ИМЯ МОДУЛЯ/model/externalapi/ИМЯ ГРУППЫ/ИМЯ МЕТОДА
    * 
    * @param bool $cache - Если true, то будет использоваться кэширование
    * @param bool $only_allowable - Если true, то будут возвращены только разрешенные в настройках модуля методы
    * @return [
    *   [
    *       'method' => 'oauth.token', //группа.метод
    *       'class' => '\ExternalApi\Model\ExternalApi\OAuth\Token' //Имя класса, выполняющего метод
    *   ],
    *   ...
    * ]
    */
    public function getApiMethods($cache = true, $only_allowable = true)
    {
        $exists_methods = $this->getAllApiMethods($cache);

        if ($only_allowable) {
            if ($this->getContextType() == self::CONTEXT_TYPE_CONFIG) {
                $allowable_methods = \RS\Config\Loader::byModule('externalapi')->allow_api_methods;
                if (!in_array(AbstractMethods\AbstractMethod::ALLOW_ALL_METHOD, $allowable_methods)) {
                    $exists_methods = array_intersect_key($exists_methods, array_flip($allowable_methods));
                }
            } else {
                $allowable_methods = $this->getContextVirtualApp()->getAllowableMethods();
                $exists_methods = array_intersect_key($exists_methods, array_flip($allowable_methods));
            }
        }
        
        return $exists_methods;
    }

    /**
     * Возвращает полный список методов API, которые существуют во всех включенных модулях текущего сайта.
     *
     * @param bool $cache Если true, то будет использоваться кэширование
     * @return array|mixed
     */
    public function getAllApiMethods($cache = true)
    {
        $site_id = SiteManager::getSiteId();

        if ($cache) {
            //Кэширование результата на уровне файлов
            $exists_methods = CacheManager::obj()
                ->request([$this, __FUNCTION__], false, $site_id);

        } else {
            $exists_methods = [];

            $module_api = new ModuleManager();
            $modules = $module_api->getActiveList($site_id);

            foreach($modules as $module_item) {
                $exists_methods += self::getModuleApiMethods($module_item);
            }
        }

        return $exists_methods;
    }
    
    /**
    * Возвращает список методов API, присутствующих в модуле
    * Классы с обработчиками методов должны находиться в папке /ИМЯ МОДУЛЯ/model/externalapi/ИМЯ ГРУППЫ/ИМЯ МЕТОДА
    * 
    * @param \RS\Module\Item $module - объект одного модуля
    * @return [
    *   [
    *       'method' => 'oauth.token', //группа.метод
    *       'class' => '\ExternalApi\Model\ExternalApi\OAuth\Token' //Имя класса, выполняющего метод
    *   ],
    *   ...
    * ]
    */
    private static function getModuleApiMethods(\RS\Module\Item $module_item)
    {
        $exists_methods = [];
        $folder = $module_item->getFolder().self::$api_method_folder; //Папка, где находятся группы методов
        if (file_exists($folder)) {
            foreach(new \DirectoryIterator($folder) as $dir) {
                if (!$dir->isDot() && $dir->isDir()) {
                    $files_iterator = new \FilesystemIterator($dir->getRealPath(), 
                                                              \FilesystemIterator::KEY_AS_PATHNAME 
                                                              | \FilesystemIterator::SKIP_DOTS);
                    
                    $files_iterator = new \RegexIterator($files_iterator, '/(.inc.php|.my.inc.php)$/');
                    
                    foreach($files_iterator as $filename => $file) {
                        $method = strtok($file->getFilename(), '.');
                        $class = str_replace('/', '\\', $module_item->getName()
                                 .self::$api_method_folder
                                 .'/'.$dir->getFilename()
                                 .'/'.$method);
                        
                        if (is_subclass_of($class, '\ExternalApi\Model\AbstractMethods\AbstractMethod')) {
                            $instance = new $class();
                            
                            //Получаем названия методов с учетом регистра букв в названии
                            $class_name = str_replace('\\', '/', get_class($instance));
                            $method_group = lcfirst(basename(dirname($class_name)));
                            $method_name = lcfirst(basename($class_name));
                            
                            $method_fullname = $method_group.'.'.$method_name;
                            $exists_methods[strtolower($method_fullname)] = [
                                'method_group' => $method_group,
                                'method_name' => $method_name,
                                'method' => $method_fullname,
                                'class' => $class
                            ];
                        }
                    }
                }
            }
        }
        return $exists_methods;
    }

    /**
     * Возвращает значение заголовка Origin для ответа на запросы
     *
     * @param string $client_name - имя приложения для подключения
     * @param string $client_version - версия приложения для подключения
     *
     * @return string
     */
    public static function getOriginForRequest($client_name = "", $client_version = "")
    {
        $origin = "*";
        if ($client_name == 'MobileSiteApp' || $client_name == 'StoreManagement'){
            $origin = 'http://localhost:8080';
        }
        //Заглушка для локальной разработки
        if (mb_stripos($_SERVER['HTTP_HOST'], "192.168.1") !== false || mb_stripos($_SERVER['HTTP_HOST'], "192.168.31") !== false){
            $origin = 'http://localhost:8100';
        }

        //Дополнительная заглушка для проверок
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }else{
            $headers = getallheaders();
        }

        $header_origin = null;
        if (isset($headers['Origin'])){
            $header_origin = $headers['Origin'];
        }

        if (isset($headers['origin'])){
            $header_origin = $headers['origin'];
        }

        if ($header_origin && (in_array($header_origin, [
                'http://localhost:8100',
                'http://localhost:8080',
                'http://localhost',
                'ionic://localhost',
                'capacitor://localhost',
            ])) && ($client_name == 'MobileSiteApp' || $client_name == 'StoreManagement')){
            $origin = $header_origin;
        }

        return $origin;
    }
}