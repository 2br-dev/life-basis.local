<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Model;

class PageSeoApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new \PageSeo\Model\Orm\PageSeo,
        [
            'multisite' => true
        ]);
    }

    /**
     * Возвращает список заголовков, мета-тегов для табличного отображения в административной панели
     *
     * @param integer $page Номер страницы
     * @param integer $page_size Количество записей на странице
     * @return \RS\Orm\AbstractObject[]
     */
    function pageSeoList($page, $page_size)
    {
        $list = $this->getList($page, $page_size);
        foreach($list as $item) {
            $route = $item->getRoute();
            $item['_id'] = $item['route_id'];
            $item['description'] = $route->getDescription();
            $item['routeview'] = $route->getPatternsView();
        }
        return $list;
    }

    /**
     * Возвращает одну запись с мета-тегами для route_id
     *
     * @param string $route_id
     * @return \RS\Orm\AbstractObject|null
     */
    public static function getPageSeo($route_id)
    {
        $api = new self();
        $api->setFilter('route_id', $route_id);
        return $api->getFirst();
    }
    
}

