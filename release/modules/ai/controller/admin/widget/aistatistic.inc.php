<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin\Widget;

use Ai\Config\ModuleRights;
use Ai\Model\StatisticApi;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Widget;
use RS\Controller\Result\Standard;
use RS\Site\Manager;

/**
 * Виджет с историей запросов к AI
 */
class AiStatistic extends Widget
{
    protected $info_title = 'Статистика запросов к ИИ';
    protected $info_description = 'Отображает график расходования запросов к ИИ';

    /**
     * Отображает статистику запросов к ИИ
     *
     * @return Standard
     */
    function actionIndex()
    {
        $site_id = Manager::getSiteId();
        $default_range = $this->url->cookie('aistatistic_range'.$site_id, TYPE_STRING, 'lastyear');
        $default_filter = $this->url->cookie('aistatistic_filter'.$site_id, TYPE_STRING, 'all');
        $default_charts = $this->url->cookie('aistatistic_charts'.$site_id, TYPE_STRING, 'all');

        $range = $this->myRequest('aistatistic_range', TYPE_STRING, $default_range);
        $filter = $this->myRequest('aistatistic_filter', TYPE_STRING, $default_filter);
        $charts = $this->myRequest('aistatistic_charts', TYPE_STRING, $default_charts);

        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers
            ->addCookie('aistatistic_range'.$site_id, $range, $cookie_expire, $cookie_path)
            ->addCookie('aistatistic_filter'.$site_id, $filter, $cookie_expire, $cookie_path)
            ->addCookie('aistatistic_charts'.$site_id, $charts, $cookie_expire, $cookie_path);

        $can_show_all_statistic = Rights::hasRight($this, ModuleRights::RIGHT_STATISTIC_SHOW_ALL);

        $api = new StatisticApi();
        $user_id = ($filter == 'my' || !$can_show_all_statistic) ? $this->user->id : null;

        if ($range == 'lastyear') {
            $order_dynamics_arr = $api->statisticLastYear($user_id);
        } else {
            $order_dynamics_arr = $api->statisticLastMonth($user_id);
        }

        $this->view->assign([
            'range' => $range,
            'filter' => $filter,
            'charts' => $charts,

            'can_show_all_statistic' => $can_show_all_statistic,
            'dynamics_arr' => $order_dynamics_arr,
            'chart_data' => json_encode([
                'points' => $order_dynamics_arr,
                'range' => $range
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $this->result->setTemplate('admin/widget/aistatistic.tpl');
    }

    /**
     * Добавляет кнопки в шапку виджета
     *
     * @return array[]
     */
    function getTools()
    {
        $router = \RS\Router\Manager::obj();
        return [
            [
                'title' => t('Перейти к статистике'),
                'class' => 'zmdi zmdi-open-in-new',
                'href' => $router->getAdminUrl(false, [], 'ai-statisticctrl')
            ]
        ];
    }
}