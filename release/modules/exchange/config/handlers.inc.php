<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Config;

use Catalog\Model\Orm\Product;
use Exchange\Model\Log\LogExchange;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Event\HandlerAbstract;
use RS\Html\Table\Type\StrYesno;
use RS\Router\Route;
use RS\Orm\Type;
use RS\Html\Filter;

/**
 * Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
 */
class Handlers extends HandlerAbstract
{
    function init()
    {
        $this->bind('getlogs');
        $this->bind('getmenus');
        $this->bind('getroute');
        $this->bind('orm.init.catalog-product');
        $this->bind('controller.exec.catalog-admin-ctrl.index');
    }

    public static function getLogs($list)
    {
        $list[] = LogExchange::getInstance();
        return $list;
    }

    public static function getRoute($routes)
    {
        $routes[] = new Route('exchange-front-gate', ['/site{site_id}/exchange/',], null, t('Шлюз обмена данными'), true);

        return $routes;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items - список пунктов меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Обмен данными с 1С'),
            'alias' => 'exchange',
            'link' => '%ADMINPATH%/exchange-ctrl/',
            'typelink' => 'link',
            'parent' => 'products',
            'sortn' => 8
        ];
        return $items;
    }

    public static function ormInitCatalogProduct(Product $product)
    {
        $product->getPropertyIterator()->append([
            t('Основные'),
            'exchange_dont_update_price' => (new Type\Integer())
                ->setMaxLength(1)
                ->setDescription(t('Не обновлять цены при обмене с 1С у данного товара'))
                ->setHint(t('Используйте данную возможность, если вы некоторым товарам персонально желаете назначить цены на сайте'))
                ->setCheckboxView(1, 0)
        ]);
    }

    /**
     * Добавлет колонки к таблице и фильтры к поиску
     *
     * @return void
     */
    public static function controllerExecCatalogAdminCtrlIndex(CrudCollection $helper)
    {
        $helper->getTableControl()
            ->getTable()
            ->addColumn(new StrYesno('exchange_dont_update_price', t('Не обновлять цены из 1С'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]), -1);

        $filter_line = (new Filter\Line())->addItems([
            new Filter\Type\Select('exchange_dont_update_price', t('Не обновлять цены из 1С?'), [
                '' => t('Не важно'),
                1 => t('Да'),
                0 => t('Нет')
            ]),
        ]);

        $helper->getFilter()->getContainer()->cleanItemsCache()->addLine($filter_line);
        $helper->getFilter()->fill(); //Переинициализируем
    }
}
