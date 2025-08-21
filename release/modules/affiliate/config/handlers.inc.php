<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Affiliate\Config;

use Affiliate\Model\AffiliateApi;
use Affiliate\Model\Behavior\ArticleArticle;
use Affiliate\Model\Behavior\CatalogWarehouse;
use Affiliate\Model\Behavior\MenuMenu;
use Affiliate\Model\MenuType\Affiliate as MenuTypeAffiliate;
use Affiliate\Model\Orm\Affiliate;
use Article\Model\Orm\Article;
use Catalog\Model\CostApi;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\WareHouseApi;
use Menu\Model\MenuType;
use Menu\Model\Orm\Menu;
use RS\Application\Application;
use RS\Config\Loader;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Exception as DbException;
use RS\Event\HandlerAbstract;
use RS\Helper\Tools;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\AbstractObject;
use RS\Orm\Type as OrmType;
use RS\Router\Manager as RouterManager;
use RS\Router\Route;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Region;

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
    function init()
    {
        $this
            ->bind('order.setdefaultaddress')
            ->bind('orm.init.catalog-warehouse')
            ->bind('orm.init.article-article')
            ->bind('orm.init.menu-menu')
            ->bind('orm.beforewrite.shop-order')
            ->bind('controller.afterinit.menu-block-menu')
            ->bind('getroute')//событие сбора маршрутов модулей
            ->bind('getmenus')//событие сбора пунктов меню для административной панели
            ->bind('getwarehouses', null, null, 10)
            ->bind('menu.gettypes')
            ->bind('getpages')
            ->bind('start')
            ->bind('initialize')
            ->bind('orm.init.shop-payment')
            ->bind('orm.beforewrite.shop-payment')
            ->bind('orm.afterload.shop-payment')
            ->bind('checkout.payment.list')
            ->bind('savemethod.payment.list', null, 'checkoutPaymentList')
            ->bind('controller.beforewrap')
            ->bind('controller.afterwrap')
            ->bind('controller.beforeexec.externalapi-front-apigate');
    }

    /**
     * Устанавливает текущий филиал для приложения
     *
     * @param $params
     * @return void
     */
    public static function controllerBeforeExecExternalApiFrontApiGate($params)
    {
        $controller = $params['controller'];
        $custom_request_params = $controller->url->request('custom', TYPE_ARRAY);
        if (isset($custom_request_params['affiliate_id'])) {
            $affiliate = new Affiliate($custom_request_params['affiliate_id']);
            if ($affiliate && $affiliate['id']) {
                AffiliateApi::setCurrentAffiliate($affiliate, true);
            }
        }

        return $params;
    }

    /**
     * Обработчик, выполняемый после выполнения котроллера.
     * Добавляет произвольный JavaScript для конкретного филиала
     *
     * @param $params
     * @return array
     */
    public static function controllerBeforewrap($params)
    {
        $router = RouterManager::obj();
        if (!$router->isAdminZone()) {
            $affiliate = AffiliateApi::getCurrentAffiliate();

            if ($affiliate['javascript_code']) {
                $params['body'] .= Tools::unEntityString($affiliate['javascript_code']);
            }

            if ($affiliate['head_code']) {
                Application::getInstance()->setAnyHeadData(Tools::unEntityString($affiliate['head_code']));
            }
        }

        return $params;
    }

    /**
     * Обработчик, выполняемый после выполнения котроллера.
     * Заменяет переменные
     *
     * @param array $params
     * @return array
     */
    public static function controllerAfterwrap($params)
    {
        $router = RouterManager::obj();
        if (!$router->isAdminZone()) {
            $affiliate = AffiliateApi::getCurrentAffiliate();
            $config = Loader::byModule(__CLASS__);
            if ($config['replace_vars_in_body']) {
                $replace = [
                    '{affiliate_title}' => $affiliate['title'],
                    '#affiliate_title' => $affiliate['title'],
                ];

                foreach($affiliate['variables'] ?: [] as $data) {
                    $replace["{affiliate_var_".$data['name']."}"] = $data['value'];
                    $replace["#affiliate_var_".$data['name']] = $data['value'];
                }

                //Сортируем по убыванию количества символов в ключе
                uksort($replace, function($a, $b) {
                    return mb_strlen($b) - mb_strlen($a);
                });

                $params['html'] = str_replace(array_keys($replace), $replace, $params['html']);
            }
            return $params;
        }
    }

    /**
     * Расширяем ORM Объекты других модулей
     */
    public static function initialize()
    {
        Article::attachClassBehavior(new ArticleArticle());
        WareHouse::attachClassBehavior(new CatalogWarehouse());
        Menu::attachClassBehavior(new MenuMenu());
    }

    /**
     * Устанавливает адрес в заказе на основе выбранного филиала
     *
     * @param array $params - параметры события
     */
    public static function orderSetDefaultAddress(array $params)
    {
        /** @var Order $order */
        $order = $params['order'];
        $affiliate = AffiliateApi::getCurrentAffiliate();
        if (!empty($affiliate['linked_region_id'])) {
            $region = new Region($affiliate['linked_region_id']);
            if ($region['id']) {
                $new_address = Address::createFromRegion($region);
                $order->setAddress($new_address);
            }
        }
    }

    /**
     * Добавляет к складу поле "Филиал"
     *
     * @param WareHouse $warehouse
     */
    public static function ormInitCatalogWarehouse(WareHouse $warehouse)
    {
        $warehouse->getPropertyIterator()->append([
            t('Основные'),
            'affiliate_id' => new OrmType\Integer([
                'description' => t('Филиал'),
                'allowEmpty' => false,
                'default' => 0,
                'tree' => [['\Affiliate\Model\AffiliateApi', 'staticTreeList'], 0, [0 => t('Не задано')]],
                'hint' => t('Информация об остатке на складе в карточке товара и оформлении заказа будет отображаться только при выборе данного филиала')
            ]),
        ]);
    }

    public static function ormInitArticleArticle(Article $article)
    {
        $article->getPropertyIterator()->append([
            t('Фильтр по городам'),
            'affiliate_id' => new OrmType\Integer([
                'description' => t('Фильтр по городам'),
                'default' => null,
                'tree' => [['\Affiliate\Model\AffiliateApi', 'staticTreeList'], 0, [0 => t('Любой город')]],
            ]),
        ]);
    }

    /**
     * Добавляем фильтр по филиалу к пунктам меню
     *
     * @param \Menu\Controller\Block\Menu $menu_block_controller
     */
    public static function controllerAfterInitMenuBlockMenu($menu_block_controller)
    {
        $affiliate = AffiliateApi::getCurrentAffiliate();
        if ($affiliate['id']) {
            $menu_block_controller->api->setFilter([
                [
                    'affiliate_id' => 0,
                    '|affiliate_id' => $affiliate['id']
                ]
            ]);
        }
    }

    /**
     * Добавим связь пунктов меню с филиалами
     *
     * @param Menu $menu
     * @return void
     */
    public static function ormInitMenuMenu(Menu $menu)
    {
        $menu->getPropertyIterator()->append([
            t('Основные'),
            'affiliate_id' => new OrmType\Integer([
                'description' => t('Филиал'),
                'allowEmpty' => false,
                'default' => 0,
                'tree' => [['\Affiliate\Model\AffiliateApi', 'staticTreeList'], 0, [0 => t('Не задано')]],
                'hint' => t('Данный пункт меню будет отображаться только при выборе указанного филиала')
            ]),
        ]);
    }

    /**
     * Сохраняет в заказе сведения о выбранном на момент оформления филиале
     *
     * @param array $params - массив с параметрами
     */
    public static function ormBeforeWriteShopOrder($params)
    {
        if (!RouterManager::obj()->isAdminZone() && $params['flag'] == AbstractObject::INSERT_FLAG) {
            $affiliate = AffiliateApi::getCurrentAffiliate();
            if ($affiliate['id']) {
                /** @var Order $order */
                $order = $params['orm'];
                $order->addExtraInfoLine(t('Выбранный город при оформлении'), $affiliate['title'], ['id' => $affiliate['id']], 'affiliate');
            }
        }
    }

    /**
     * Возвращает маршруты данного модуля. Откликается на событие getRoute.
     * @param array $routes - массив с объектами маршрутов
     * @return array of \RS\Router\Route
     */
    public static function getRoute(array $routes)
    {
        $routes[] = new Route('affiliate-front-change', '/change-affiliate/{affiliate}/', null, t('Смена текущего филиала'));
        $routes[] = new Route('affiliate-front-contacts', '/contacts/{affiliate}/', null, t('Контакты филиала'));
        $routes[] = new Route('affiliate-front-affiliates', '/affiliates/', null, t('Выбор филиалов'));
        $routes[] = new Route('affiliate-front-robots', '/robots.txt', null, t('Виртуальный robots.txt'), true);

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
            'title' => t('Филиалы в городах'),
            'alias' => 'affiliate',
            'link' => '%ADMINPATH%/affiliate-ctrl/',
            'parent' => 'modules',
            'typelink' => 'link',
        ];
        return $items;
    }

    /**
     * Обрабатывает событие выборки доступных складов
     *
     * @param array $params
     */
    public static function getWarehouses($params)
    {
        $affiliate = AffiliateApi::getCurrentAffiliate();
        if ($affiliate['id']) {
            /** @var WareHouseApi $warehouse_api */
            $warehouse_api = $params['warehouse_api'];
            $warehouse_api->setFilter([
                [
                    'affiliate_id' => 0,
                    '|affiliate_id' => $affiliate['id']
                ]
            ]);
        }
    }

    /**
     * Добавляет в систему собственный тип меню
     *
     * @param MenuType\AbstractType[] $types
     * @return MenuType\AbstractType[]
     */
    public static function menuGetTypes($types)
    {
        $types[] = new MenuTypeAffiliate();
        return $types;
    }

    /**
     * Устанавливает тип цен по умолчанию
     */
    public static function start()
    {
        $config = ConfigLoader::byModule('affiliate');
        if (!RouterManager::obj()->isAdminZone() && $config['installed']) {
            $affiliate = AffiliateApi::getCurrentAffiliate();
            if ($affiliate['cost_id']) {
                CostApi::setSessionDefaultCost($affiliate['cost_id']);
            }
        }
    }

    /**
     * Добавляет страницы контактов филиалов в sitemap.xml
     *
     * @param array $pages - список
     * @return array
     * @throws DbException
     */
    public static function getPages($pages)
    {
        $api = new AffiliateApi();
        $api->setFilter([
            'public' => 1,
            'clickable' => 1
        ]);

        $list = $api->getListAsArray();

        $router = RouterManager::obj();
        foreach ($list as $item) {
            $url = $router->getUrl('affiliate-front-contacts', ['affiliate' => $item['alias']]);
            $pages[$url] = [
                'loc' => $url
            ];
        }
        return $pages;
    }

    /**
     * Корректирует способы оплаты согласно установленному филиалу
     *
     * @param $data
     * @return void
     */
    public static function checkoutPaymentList($data)
    {
        $payment_list = $data['list'];
        $current_affiliate = AffiliateApi::getCurrentAffiliate();

        if (isset($current_affiliate['id'])  && $payment_list) {
            $payment_list_to_affiliate = [];
            foreach ($payment_list as $key => $payment) {
                if (empty($payment['affiliate_id_arr'])
                    || in_array($current_affiliate['id'], $payment['affiliate_id_arr']))
                {
                    $payment_list_to_affiliate[$key] = $payment;
                }
            }

            $data['list'] = $payment_list_to_affiliate;
        }

        return $data;
    }

    /**
     * Добавляет к объекту оплаты возможность выбрать филиал отображения цены
     *
     * @param Payment $orm
     * @return void
     */
    public static function ormInitShopPayment(Payment $orm)
    {
        $orm->getPropertyIterator()->append([
            t('Дополнительные условия показа'),
            'affiliate_id' => new OrmType\Text([
                'visible' => false,
            ]),
            'affiliate_id_arr' => new OrmType\ArrayList([
                'tree' => [['\Affiliate\Model\AffiliateApi', 'staticTreeList']],
                'description' => t('Филиал'),
                'runtime' => true,
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]],
            ]),
        ]);
    }

    /**
     * После загрузки объекта Shop - Payment
     *
     * @param $param
     */
    public static function ormAfterLoadShopPayment($param)
    {
        /**
         * @var Payment $payment
         */
        $payment = $param['orm'];
        if (!empty($payment['affiliate_id'])) {
            $payment['affiliate_id_arr'] = @unserialize((string)$payment['affiliate_id']);
        }
    }

    /**
     * Перед записью объекта Shop - Payment
     *
     * @param $data
     */
    public static function ormBeforeWriteShopPayment($data)
    {
        /**
         * @var Payment $payment
         */
        $payment = $data['orm'];
        if($payment->isModified('affiliate_id_arr')){
            $payment['affiliate_id'] = serialize($payment['affiliate_id_arr']);
        }
    }
}
