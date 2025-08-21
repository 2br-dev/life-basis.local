<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Controller\Front;
use ExternalApi\Model;
use ExternalApi\Model\AbstractException;
use ExternalApi\Model\ApiRouter;
use ExternalApi\Model\ErrorManager;
use ExternalApi\Model\LogApi;
use ExternalApi\Model\Orm\Log;
use ExternalApi\Model\Exception as ApiException;
use RS\Controller\Front;
use RS\Img\Core as ImgCore;
use RS\Language\Core;

/**
* Контроллер, встречающий запросы к методам API и отображающий справку по методам API
*/
class ApiGate extends Front
{                
    public $method;
    public $lang;
    public $default_version;
    public $version;
    public ApiRouter $api_router;
    //из ответов мобильного приложения
    public $client_version;
    public $client_name;
    public $api_key;

    function init()
    {
        $this->wrapOutput(false);
        $config = $this->getModuleConfig();

        if (!$config->enabled) {
            $this->e404(); //Запрещаем обращение к API, если модуль выключен
        }

        $this->default_version = !empty($config['default_api_version']) ? $config['default_api_version']: 1;
        $this->method          = $this->url->request('method', TYPE_STRING);
        $this->lang            = $this->url->request('lang', TYPE_STRING, Model\AbstractMethods\AbstractMethod::DEFAULT_LANGUAGE);
        
        $this->view->assign([
            'lang' => $this->lang
        ]);
        
        //Устанавливаем язык
        if (Core::getCurrentLang() != $this->lang) {
            Core::setSystemLang($this->lang);
            Core::init();
        }
        
        $this->version = $this->request('v', TYPE_STRING, $this->default_version);
        $this->api_router = new ApiRouter($this->version, $this->lang);

        try {
            $this->api_key = $this->url->get('api_key', TYPE_STRING);
            $this->api_router->setContextByApiKey($this->api_key);
        } catch (ApiException $e) {
            $this->e404(); //Запрещаем обращение к API, если API-ключ некорректный
        }

        $config = $this->getModuleConfig();
        //отключаем webp, если указано в настройках модуля
        if ($config['disable_image_webp']) {
            ImgCore::switchFormat(ImgCore::FORMAT_WEBP, false);
        }
        //Проверяем допустимый домен
        $allow_domain = $config->allow_domain;
        if ($allow_domain && $allow_domain != $this->url->server('HTTP_HOST')) {
            $this->e404(t('Неверный домен для обращения к API'));
        }

        //Получим заголовки приложения
        if (isset($_GET['client_version'])){ //Если это в параметрах
            $this->client_version = $this->url->request('client_version', TYPE_FLOAT, 0);
            $this->client_name    = $this->url->request('client_name', TYPE_STRING, '');
        }else{ //Если нет, то смотрим в заголовках
            $this->client_version = $this->url->server('X-Client-Version', TYPE_FLOAT, 0);
            $this->client_name    = $this->url->server('X-Client-Name', TYPE_STRING, '');
        }
    }
        
    /**
    * Выполняет метод API
    */
    function actionIndex()
    {
        $start_time = microtime(true);
        $format = $this->url->convert( $this->url->request('format', TYPE_STRING), [
            'json'
        ]);

        try {
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                $result = 'Run this api method width other than OPTIONS HTTP METHOD';
            } else {
                $params = $this->api_router->makeParams($this->method, $this->url);
                $result = $this->api_router->runMethod($this->method, $params);
            }
                        
        } catch(AbstractException $e) {
            
            $result = $e->getApiError();
        }

        //Пишем запрос в лог
        $execution_time = microtime(true) - $start_time;
        LogApi::writeToLog($this->url, $this->method, $params ?? [], $result, $execution_time);
        Log::removeOldItems();

        $origin = ApiRouter::getOriginForRequest($this->client_name, $this->client_version);

        $this->app->headers
                        ->addHeader('Access-Control-Allow-Origin', $origin)
                        ->addHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                        ->addHeader('Access-Control-Allow-Credentials', 'true')
                        ->addHeader('Access-Control-Allow-Headers', '*, x-client-name, x-client-version')
                        ->addHeader('Content-type', 'application/json; charset=utf-8');
        
        return Model\ResultFormatter::format($result, $format);
    }
    
    /**
    * Показывает справку по методу или всем методам API
    */
    function actionHelp()
    {
        if (($this->api_router->getContextType() == ApiRouter::CONTEXT_TYPE_VIRTUAL
            && !$this->api_router->getContextVirtualApp()->vapp_endpoint_enable_api_help)
            || !$this->getModuleConfig()->enable_api_help)
        {
            $this->e404(t('Раздел документации отключен'));
        }
        
        $method = $this->url->request('method', TYPE_STRING);
        
        if ($method == 'errors') {
            $this->view->assign([
                'exceptions' => ErrorManager::getExceptionClasses()
            ]);
            
            $template = 'help_errors.tpl';
        } elseif ($method) {
            if ($module_instance = $this->api_router->getMethodInstance($method, true)) {
                //Просмотр одного метода
                $this->view->assign([
                    'method' => $method,
                    'method_info' => $module_instance->getInfo($this->lang),
                    'api_key' => $this->api_key
                ]);
                $template = 'help_method.tpl';
            } else {
                $this->e404(t('Метод не найден'));
            }
        } else {
            //Оглавление методов
            $this->view->assign([
                'grouped_methods' => $this->api_router->getGroupedMethodsInfo($this->lang),
                'current_version' => $this->default_version,
                'languages' => $this->api_router->getMethodsLanguages(),
                'api_key' => $this->api_key
            ]);
            $template = 'help_method_list.tpl';
        }
        return $this->view->fetch($template);
    }
}