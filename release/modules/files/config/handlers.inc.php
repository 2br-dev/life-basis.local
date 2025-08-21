<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Config;
use Files\Model\FileApi;
use \RS\Router;
use RS\Site\Manager;

/**
* Класс обработчиков событий
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('getroute');
        $this->bind('cron');
        
        //Подключаем обработку событий товаров
        $handlers_product = new HandlersProduct();
        $handlers_product->init();
        
        //Подключаем обработку событий заказов
        $handlers_order = new HandlersOrder();
        $handlers_order->init();
    }
    
    /**
    * Добавляет маршрут в систему
    * 
    * @param Router\Route[] $routes
    * @return Router\Route[]
    */
    public static function getRoute($routes)
    {
        $routes[] = new Router\Route('files-front-download', [
            '/download-file/h/{uniq}\.{extension}',
            '/download-file/{uniq_name}'
        ], null, t('Блок файлов: скачивание файла'));
        $routes[] = new Router\Route('files-front-upload', '/upload-file/{linkType}/{Act}/', null, t('Блок файлов: загрузка файла'));
        return $routes;
    }

    /**
     * Удаляет не связанные ни с одним объектом файлы
     *
     * @param array $params
     */
    public static function cron($params)
    {
        foreach(Manager::getSiteList() as $site) {
            if (in_array(120, $params['minutes'])) {
                $api = new FileApi();
                $api->setSiteContext($site['id']);
                $api->cleanOldUnlinkedFiles();
            }
        }
    }
    
}