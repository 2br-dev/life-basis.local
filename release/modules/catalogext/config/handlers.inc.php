<?php

namespace catalogext\Config;

use RS\Event\HandlerAbstract;
use RS\Router\Route as RouterRoute;
use RS\Orm\Type;

/**
 * Класс содержит обработчики событий, на которые подписан модуль
 */
class Handlers extends HandlerAbstract
{
    /**
     * Добавляет подписку на события
     *
     * @return void
     */
    public function init()
    {
        $this->bind('getroute');  //событие сбора маршрутов модулей
        $this->bind('getmenus'); //событие сбора пунктов меню для административной панели
        $this->bind('orm.init.catalog-product');
    }

    public static function ormInitCatalogProduct(\Catalog\Model\Orm\Product $product){
        $product->getPropertyIterator()->append([
            t('Дополнительно'),
            'longtitle' => new Type\Varchar([
                'description' => 'Расширенный заголовок'
            ])
        ]);
    }

    /**
     * Возвращает маршруты данного модуля. Откликается на событие getRoute.
     * @param array $routes - массив с объектами маршрутов
     * @return array of \RS\Router\Route
     */
    public static function getRoute(array $routes)
    {
        $routes[] = new RouterRoute('catalogext-front-ctrl', [
            '/testmodule-catalogext/',
        ], null, 'Роут модуля catalogext');

        return $routes;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     * @param array $items - массив с пунктами меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => 'Пункт модуля catalogext',
            'alias' => 'catalogext-control',
            'link' => '%ADMINPATH%/catalogext-control/',
            'parent' => 'modules',
            'sortn' => 40,
            'typelink' => 'link',
        ];
        return $items;
    }
}
