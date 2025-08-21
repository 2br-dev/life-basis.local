<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Config;
use RS\Event\HandlerAbstract;
use RS\Router\Route;
use RS\Site\Manager;
use Support\Model\CrawlerProfileApi;
use Support\Model\Log\LogSupport;
use Support\Model\Mail\ImapCrawler;
use Support\Model\Platform\PlatformEmail;
use Support\Model\Platform\PlatformMobileSiteApp;
use Support\Model\Platform\PlatformSite;
use Support\Model\TopicApi;

class Handlers extends HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getmenus')
            ->bind('getroute')
            ->bind('getlogs')
            ->bind('meter.recalculate')
            ->bind('support.getPlatforms')
            ->bind('cron');
    }

    /**
     * Планировщик
     *
     * @param $params
     */
    public static function cron($params)
    {
        if ($modules = CrawlerProfileApi::getUnexistsModules()) {
            echo t('Отсутствуют модули %0, необходимые для успешной сборки почты', [implode(', ', $modules)]);
            return;
        }

        $crawlers_api = new CrawlerProfileApi();
        foreach(Manager::getSiteList() as $site) {
            $crawlers_api->setSiteContext($site['id']);
            $crawlers_api->resetQueryObject();
            $is_manual_run = $crawlers_api->isNeedRunCrawlerMark();

            foreach($crawlers_api->getList() as $profile) {
                if (!$profile['is_enable']) continue;

                $time_to_run = false;
                if ($profile['crawl_interval_min']) {
                    foreach ($params['minutes'] as $minute) {
                        if ($minute % $profile['crawl_interval_min'] == 0) {
                            $time_to_run = true;
                            break;
                        }
                    }
                }

                if ($time_to_run || $is_manual_run) {
                    echo t("Запуск получения почты для профиля %0\n", [$profile['title']]);

                    $crawler = new ImapCrawler($profile);
                    $result = $crawler->fetchMail();

                    if (is_numeric($result)) {
                        echo t("Завершено. Загружено %0 писем, будет продолжено в след. итерации\n", [$profile['title']]);
                    } elseif ($result === false) {
                        echo t("Завершено с ошибкой: %0\n", [$crawler->getErrorsStr()]);
                    } else {
                        echo t("Завершено получение писем успешно\n");
                    }

                    if ($result === true && $is_manual_run) {
                        $crawlers_api->removeNeedRunCrawlerMark();
                    }
                }
            }
        }
    }

    /**
     * Возвращает маршруты данного модуля
     *
     * @param array $routes
     * @return array
     */
    public static function getRoute(array $routes) 
    {        
        $routes[] = new Route('support-front-support', [
            '/my/support/{Act}/n-{number}/',
            '/my/support/{Act}/{id}/', //Маршрут для совместимости со старыми версиями ReadyScript
            '/my/support/{Act}/',
            '/my/support/'
        ], null, t('Поддержка'));
        return $routes;
    }

    /**
     * Возвращает счетчик непросмотренных объектов
     *
     * @param $meters
     * @return
     */
    public static function meterRecalculate($meters)
    {
        $topic_api = new TopicApi();
        $topic_meter_api = $topic_api->getMeterApi();
        $meters[$topic_meter_api->getMeterId()] = $topic_meter_api->getUnviewedCounter();

        return $meters;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Поддержка'),
                'alias' => 'support',
                'link' => '%ADMINPATH%/support-topicsctrl/',
                'typelink' => 'link',                      
                'parent' => 'crm',
                'sortn' => 100
        ];
        return $items;
    }

    /**
     * Дополняет список платформ
     *
     * @param [] $list
     * @return array
     */
    public static function supportGetPlatforms($list)
    {
        $list[] = new PlatformSite();
        $list[] = new PlatformEmail();
        $list[] = new PlatformMobileSiteApp();
        return $list;
    }

    /**
     * Дополняет список классов логирования
     *
     * @param [] $list
     * @return array
     */
    public static function getLogs($list)
    {
        $list[] = LogSupport::getInstance();
        return $list;
    }
}