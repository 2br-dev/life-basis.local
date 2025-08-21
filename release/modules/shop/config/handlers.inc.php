<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Config;

use Alerts\Model\Manager as AlertsManager;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\Orm\WareHouseGroup;
use Crm\Model\Autotask\Ruleif\{CreateOrder, CreateReservation};
use Crm\Model\Autotask\RuleIfOrder;
use Crm\Model\Autotask\RuleIfReservation;
use Crm\Model\Links\Type\{LinkTypeOrder, LinkTypeReservation};
use RS\Application\Auth;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Db\Exception as DbException;
use RS\Event\HandlerAbstract;
use RS\Event\Manager;
use RS\Exception;
use RS\Exception as RSException;
use RS\Log\AbstractLog;
use RS\Html\Filter;
use RS\Html\Table\Type as TableType;
use RS\Module\AbstractModel\TreeList;
use RS\Module\Item as ModuleItem;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;
use RS\Router\Manager as RouterManager;
use RS\Router\Route;
use Shop\Config\File as ShopConfig;
use Shop\Model\ArchiveOrderApi;
use Shop\Model\BonusCardType\BonusCard\RemoteBonusCard;
use Shop\Model\CashRegisterApi;
use Shop\Model\CsvSchema as ShopCsvSchema;
use Shop\Model\Behavior as ShopBehavior;
use Shop\Model\Cart;
use Shop\Model\DeliveryType;
use Shop\Model\Log\LogCashRegister;
use Shop\Model\Log\LogDelivery;
use Shop\Model\Log\LogDeliveryCdek;
use Shop\Model\Log\LogOnlinePay;
use Shop\Model\Log\LogPaymentYandexKassaApi;
use Shop\Model\Log\LogTrueApi;
use Shop\Model\Marking\TrueApi\CheckCodes;
use Shop\Model\OrderApi;
use Shop\Model\Orm\CartItem;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\UserStatus;
use Shop\Model\PaymentType;
use Shop\Model\PrintForm;
use Shop\Model\ProductsReturnApi;
use Shop\Model\ReceiptApi;
use Shop\Model\ReservationApi;
use Shop\Model\BonusCardType\BonusCard\BonusCard;
use Users\Model\Orm\User;
use Users\Model\Orm\UserGroup;

class Handlers extends HandlerAbstract
{
    /**
     * Инициализация модуля
     */
    function init()
    {
        $this->bind('api.oauth.token.success');
        $this->bind('controller.exec.users-admin-ctrl.index');
        $this->bind('controller.exec.catalog-admin-ctrl.index');
        $this->bind('crm.deal.getlinktypes');
        $this->bind('crm.interaction.getlinktypes');
        $this->bind('crm.getifrules');
        $this->bind('crm.autotask.getifrules');
        $this->bind('crm.task.getlinktypes');
        $this->bind('cron');
        $this->bind('delivery.gettypes');
        $this->bind('getmenus');
        $this->bind('getlogs');
        $this->bind('getroute');
        $this->bind('initialize');
        $this->bind('meter.recalculate');
        $this->bind('orm.init.catalog-dir');
        $this->bind('orm.init.catalog-product');
        $this->bind('orm.init.catalog-warehouse');
        $this->bind('orm.init.catalog-warehousegroup');
        $this->bind('orm.init.users-user');
        $this->bind('orm.init.users-usergroup');
        $this->bind('payment.gettypes');
        $this->bind('printform.getlist');
        $this->bind('user.auth');
        $this->bind('bonuscards.getproviders');

        if (\Setup::$INSTALLED) {
            $this->bind('orm.afterwrite.site-site', $this, 'onSiteCreate');
        }
    }

    /**
     * Добавляет в систему провайдеры бонусных карт
     */
    public static function bonusCardsGetProviders($list)
    {
        $list[] = new BonusCard();
        $list[] = new RemoteBonusCard();
        return $list;
    }

    /**
     * Возвращает классы логирования этого модуля
     *
     * @param AbstractLog[] $list - список классов логирования
     * @return AbstractLog[]
     */
    public static function getLogs($list)
    {
        $list[] = LogCashRegister::getInstance();
        $list[] = LogDeliveryCdek::getInstance();
        $list[] = LogDelivery::getInstance();
        $list[] = LogPaymentYandexKassaApi::getInstance();
        $list[] = LogOnlinePay::getInstance();
        $list[] = LogTrueApi::getInstance();

        return $list;
    }

    /**
     * Расширяем объект склада
     *
     * @param Warehouse $warehouse
     */
    public static function ormInitCatalogWarehouse(Warehouse $warehouse)
    {
        $warehouse->getPropertyIterator()->append([
            t('Основные'),
            'linked_region_id' => (new Type\Integer())
                ->setDescription('Город меcтонахождения')
                ->setTree('\Shop\Model\RegionApi::staticTreeList', 0, [0 => t('- Не выбран -')])
                ->setAttr([
                    TreeList\TreeListOrmIterator::ATTRIBUTE_DISALLOW_SELECT_BRANCHES => true,
                ])
        ]);
    }

    /**
     * Расширяем объект группы складов
     *
     * @param WareHouseGroup $wareHouseGroup
     */
    public static function ormInitCatalogWarehouseGroup(WareHouseGroup $wareHouseGroup)
    {
        $wareHouseGroup->getPropertyIterator()->append([
            t('Основные'),
            'delivery_delay' => new Type\Integer([
                'description' => t('Смещение сроков доставки на N дней'),
                'hint' => t('Укажите кол-во дней, которые будут прибавлены к срокам доставки'),
            ]),
        ]);
    }

    /**
     * Расширяем поведение объекта Пользователь
     */
    public static function initialize()
    {
        User::attachClassBehavior(new ShopBehavior\UsersUser);
        Product::attachClassBehavior(new ShopBehavior\CatalogProduct);
        WareHouse::attachClassBehavior(new ShopBehavior\CatalogWarehouse);
    }

    /**
     * Добавляет в CRM взаимодействия возможность связи с заказом
     *
     * @param array $link_types
     * @return array
     * @throws RSException
     */
    public static function crmInteractionGetLinkTypes($link_types)
    {
        $link_types[] = LinkTypeOrder::getId();
        return $link_types;
    }

    /**
     * Добавляет в CRM сделки возможность связи с предзаказом
     *
     * @param array $link_types
     * @return array
     * @throws RSException
     */
    public static function crmDealGetLinkTypes($link_types)
    {
        $link_types[] = LinkTypeOrder::getId();
        $link_types[] = LinkTypeReservation::getId();
        return $link_types;
    }

    /**
     * Добавляет в CRM задачи возможность связи с заказом и презаказом
     *
     * @param array $link_types
     * @return array
     * @throws RSException
     */
    public static function crmTaskGetLinkTypes($link_types)
    {
        $link_types[] = LinkTypeOrder::getId();
        $link_types[] = LinkTypeReservation::getId();
        return $link_types;
    }

    /**
     * Регистрируем класс условия для автозадач
     *
     * @param $rules
     * @return []
     */
    public static function crmAutoTaskGetIfRules($rules)
    {
        $rules[] = new RuleIfOrder();
        $rules[] = new RuleIfReservation();

        return $rules;
    }

    /**
     * Добавляет возможность создания автозадач при создании заказа или предзаказа
     *
     * @param array $list
     * @return array
     */
    public static function crmGetIfRules($list)
    {
        $list[] = new CreateOrder();
        $list[] = new CreateReservation();

        return $list;
    }


    /**
     * Добавляем информацию о количестве непросмотренных заказов
     * во время вызова события пересчета счетчиков
     *
     * @param array $meters - параметры метрики
     * @return mixed
     */
    public static function meterRecalculate($meters)
    {
        $order_api = new OrderApi();
        $order_meter_api = $order_api->getMeterApi();
        $meters[$order_meter_api->getMeterId()] = $order_meter_api->getUnviewedCounter();

        $reservation_api = new ReservationApi();
        $reservation_meter_api = $reservation_api->getMeterApi();
        $meters[$reservation_meter_api->getMeterId()] = $reservation_meter_api->getUnviewedCounter();

        $return_api = new ProductsReturnApi();
        $return_meter_api = $return_api->getMeterApi();
        $meters[$return_meter_api->getMeterId()] = $return_meter_api->getUnviewedCounter();

        return $meters;
    }

    /**
     * Добавляем к сведениям об авторизации, сведения о том, является ли пользователь курьером
     * @param array $params - параметры удачной авторизации
     * @return array
     */
    public static function apiOauthTokenSuccess($params)
    {
        $config = ConfigLoader::byModule(__CLASS__);
        $user_id = $params['result']['response']['user']['id'];

        $courier_user_group = $config['courier_user_group'];
        $user = new User($user_id);
        $params['result']['response']['user']['is_courier'] = in_array($courier_user_group, $user->getUserGroups());

        return $params;
    }

    /**
     * Расширяет функционал контроллера админ панели товаров
     *
     * @param CrudCollection $crud_collection - объект контроллера
     */
    public static function controllerExecCatalogAdminCtrlIndex(CrudCollection $crud_collection)
    {
        /** @var \RS\Html\Table\Control $table */
        $table = $crud_collection['table'];
        $table->getTable()->addColumn(new TableType\Text('reservation', t('Предзаказ'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]), -1);
    }

    /**
     * Расширяет функционал контроллера админ панели пользователей
     *
     * @param CrudCollection $crud_collection - объект контроллера
     */
    public static function controllerExecUsersAdminCtrlIndex(CrudCollection $crud_collection)
    {
        //Добавим колонку "Баланс" в таблицу с пользователями
        /** @var \RS\Html\Table\Control $table */
        $table = $crud_collection['table'];
        $table->getTable()->addColumn(new TableType\Text('balance', t('Баланс'), ['Sortable' => SORTABLE_BOTH]), -1);

        //Добавим фильтр по балансу
        /** @var \RS\Html\Filter\Control $filter */
        $filter = $crud_collection['filter'];

        $container = $filter->getContainer();
        $lines = $container->getLines();
        $lines[0]->addItem(new Filter\Type\Text('balance', t('Баланс'), [
            'showType' => true
        ]));
        $container->cleanItemsCache();

        //Добавим действия в таблицу
        $router = RouterManager::obj();
        $columns = $table->getTable()->getColumns();
        foreach ($columns as $column) {
            if ($column instanceof TableType\Actions) {
                foreach ($column->getActions() as $action) {
                    if ($action instanceof TableType\Action\DropDown) {
                        $action->addItem([
                            'title' => t('история транзакций'),
                            'attr' => [
                                '@href' => $router->getAdminPattern(false, [':f[user_id]' => '@id'], 'shop-transactionctrl'),
                            ]
                        ]);

                        $action->addItem([
                            'title' => t('исправить баланс'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $router->getAdminPattern('fixBalance', [':id' => '@id'], 'shop-balancectrl'),
                            ]
                        ]);
                    }
                }
                $column->addAction(new TableType\Action\Action($router->getAdminPattern('addfunds', [':id' => '~field~', 'writeoff' => 0], 'shop-balancectrl'), t('пополнить баланс'), ['iconClass' => 'money', 'class' => 'crud-add']), 0);
                $column->addAction(new TableType\Action\Action($router->getAdminPattern('addfunds', [':id' => '~field~', 'writeoff' => 1], 'shop-balancectrl'), t('списать с баланса'), ['iconClass' => 'money-off', 'class' => 'crud-add']), 0);
            }
        }
    }

    /**
     * Возвращает маршруты данного модуля
     *
     * @param array $routes - ранее установленые маршруты
     * @return array
     */
    public static function getRoute(array $routes)
    {
        /** @var ShopConfig $config */
        $config = ConfigLoader::byModule('shop');

        //Оформление заказа
        switch ($config->getCheckoutType()) {
            case ShopConfig::CHECKOUT_TYPE_FOUR_STEP:
                $checkout_controller = 'shop-front-checkout';
                break;
            case ShopConfig::CHECKOUT_TYPE_ONE_PAGE:
            case ShopConfig::CHECKOUT_TYPE_CART_CHECKOUT:
            default:
                $checkout_controller = 'shop-front-cartcheckout';
        }
        $routes[] = new Route('shop-front-checkout', ['/checkout/{Act}/', '/checkout/'], ['controller' => $checkout_controller], t('Оформление заказа'));
        $routes[] = new Route('shop-block-checkout', '/block-checkout/', null, t('Блок оформления заказа'));
        //Корзина
        $routes[] = new Route('shop-front-cartpage', '/cart/', null, t('Корзина'));
        $routes[] = new Route('shop-block-cartfull', '/block-cart/', null, t('Блок корзины'));
        //Выбор многомерной комплектации
        $routes[] = new Route('shop-front-multioffers', '/multioffers/{product_id}/', null, t('Выбор многомерной комплектации'));
        //Документ на оплату
        $routes[] = new Route('shop-front-documents', '/paydocuments/', null, t('Документ на оплату'));
        $routes[] = new Route('shop-front-printdocs', [
            '/printdocuments/{Act:(deliverydocs)}/{order_hash}/{doc_type}/{delivery_order_id}/',
            '/printdocuments/{order_hash}/{doc_type}/'
        ], null, t('Документы по заказам'));
        //Лицензионное соглашение
        $routes[] = new Route('shop-front-licenseagreement', '/license-agreement/', null, t('Лицензионное соглашение'));
        //Просмотр заказа
        $routes[] = new Route('shop-front-myorderview', [
            '/my/orders/view-{order_id}/',
            '/my/orders/{Act:(delete|changepayment)}-{order_id}/'
        ], null, t('Просмотр заказа'));
        //Мои заказы
        $routes[] = new Route('shop-front-myorders', ['/my/orders/'], null, t('Мои заказы'));
        //Мои возвраты
        $routes[] = new Route('shop-front-myproductsreturn', ['/my/productsreturn/{Act:(add|edit|delete|print|view|rules)}/', '/my/productsreturn/'], null, t('Мои возвраты'));
        //Мои привязанные карты
        $routes[] = new Route('shop-front-mysavedpaymentmethods', ['/my/payment-methods/'], null, t('Мои привязанные карты'));
        //Лицевой счет
        $routes[] = new Route('shop-front-mybalance', ['/my/balance/{Act}/', '/my/balance/'], null, t('Лицевой счет'));
        //Веб-хуки доставок
        $routes[] = new Route('shop-front-deliverywebhooks', ['/deliverywebhooks/{DeliveryType}/'], null, t('Веб-хуки доставок'));
        //Online платежи
        $routes[] = new Route('shop-front-onlinepay', ['/onlinepay/{PaymentType}/{Act:(success|fail|result|status)}/', '/onlinepay/{Act}/'], null, t('Online платежи'));
        //Список регионов
        $routes[] = new Route('shop-front-regiontools', '/regiontools/', null, t('Список регионов'), true);
        //Предварительный заказ товара
        $routes[] = new Route('shop-front-reservation', '/reservation/{product_id}/', null, t('Предварительный заказ товара'));
        //Контроллер для приёма команд от касс онлайн
        $routes[] = new Route('shop-front-cashregister', [
            '/cashregister/{CashRegisterType}/{Act}/',
            '/cashregister/{CashRegisterType}/'
        ], null, t('Шлюз обмена данными с кассами'), true);
        //Предварительный заказ товара
        $routes[] = new Route('shop-front-selectedaddresschange', '/address-change/', null, t('Смена выбранного города'));

        return $routes;
    }

    /**
     * Привязывает корзину к пользователю после авторизации
     *
     * @param array $params - массив параметров с объектами пользователя
     */
    public static function userAuth($params)
    {
        /** @var User $user */
        $user = $params['user'];

        $guest_id = Auth::getGuestId();
        //Привязываем корзину к пользователю
        $cart = Cart::currentCart();
        $items = $cart->getCartItemsByType();
        $has_current_session_items = false;
        foreach ($items as $item) {
            if ($item['session_id'] == $guest_id) {
                $has_current_session_items = true;
                break;
            }
        }
        if ($has_current_session_items) {
            //Если будучи неавторизованным, пользователь собрал новую корзину,
            //то НЕ импортируем корзину от авторизованного пользователя
            (new OrmRequest)
                ->delete()
                ->from(new CartItem())
                ->where("user_id = '#user_id' AND session_id != '#session_id'", [
                    'session_id' => $guest_id,
                    'user_id' => $user['id']
                ])
                ->exec();
        } else {
            //Если текущая корзина пользователя пуста, а у авторизованного пользователя была собрана,
            //то импортируем её
            (new OrmRequest)
                ->update(new CartItem())
                ->set([
                    'session_id' => $guest_id
                ])->where([
                    'user_id' => $user['id']
                ])->exec();

            $cart->cleanInfoCache();
        }

        (new OrmRequest)
            ->update(new CartItem())
            ->set([
                'user_id' => $user['id']
            ])->where([
                'session_id' => $guest_id
            ])->exec();

        Cart::destroy();
    }

    /**
     * Возвращает процессоры(типы) доставки, присутствующие в текущем модуле
     *
     * @param array $list - массив из передаваемых классов доставок
     * @return array
     */
    public static function deliveryGetTypes($list)
    {
        $list[] = new DeliveryType\FixedPay();
        $list[] = new DeliveryType\Myself();
        $list[] = new DeliveryType\Manual();
        $list[] = new DeliveryType\RussianPost();
        $list[] = new DeliveryType\Universal();
        $list[] = new DeliveryType\Cdek2();
        $list[] = new DeliveryType\Cdek();
        $list[] = new DeliveryType\RussianPostCalc();
        return $list;
    }

    /**
     * Возвращает способы оплаты, присутствующие в текущем модуле
     *
     * @param array $list - массив из передаваемых классов оплат
     * @return array
     */
    public static function paymentGetTypes($list)
    {
        $list[] = new PaymentType\Cash();
        $list[] = new PaymentType\Bill();
        $list[] = new PaymentType\FormPd4();
        $list[] = new PaymentType\Robokassa();
        $list[] = new PaymentType\Assist();
        $list[] = new PaymentType\PayPal();
        $list[] = new PaymentType\YandexMoney();
        $list[] = new PaymentType\YandexKassaApi();
        $list[] = new PaymentType\PersonalAccount();
        $list[] = new PaymentType\Toucan();
        return $list;
    }

    /**
     * Обрабатывает событие - создание сайта
     *
     * @param array $params - массив параметров с объектом сайта
     */
    public static function onSiteCreate($params)
    {
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {

            $site = $params['orm'];
            UserStatus::insertDefaultStatuses($site['id']); //Добавляем статусы заказов по-умолчанию

            $module = new ModuleItem('shop');
            /** @var Install $installer */
            $installer = $module->getInstallInstance();
            $installer->importCsv(new ShopCsvSchema\Region(), 'regions', $site['id']);
            $installer->importCsv(new ShopCsvSchema\Zone(), 'zones', $site['id']);
            $installer->importCsv(new ShopCsvSchema\SubStatus(), 'substatus', $site['id']);
        }
    }

    /**
     * Добавляем раздел "Налоги" в карточку товара
     *
     * @param Product $orm_product - объект товара
     */
    public static function ormInitCatalogProduct(Product $orm_product)
    {
        $orm_product->getPropertyIterator()->append([
            t('Основные'),
            'disallow_manually_add_to_cart' => new Type\Integer([
                'description' => t('Запретить ручное добавление товара в корзину'),
                'hint' => t('Может использоваться для подарочных товаров, которые автоматически добавляются в корзину'),
                'maxLength' => '1',
                'checkboxView' => [1, 0],
                'default' => 0,
            ]),
            'payment_subject' => new Type\Varchar([
                'description' => t('Признак предмета расчета для чека'),
                'hint' => t('Признак предмета расчета для печати в чеке по ФЗ-54'),
                'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentSubjects']],
                'default' => CashRegisterApi::PAYMENT_SUBJECT_COMMODITY,
            ]),
            'payment_method' => new Type\Varchar([
                'description' => t('Признак способа расчета для чека'),
                'hint' => t('Если будет задан, то будет перекрывать все остальные настройки (в оплате и настройках модуля Магазин)'),
                'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentMethods'], [0 => t('По умолчанию')]],
                'allowEmpty' => false,
                'default' => 0,
            ]),
            'reservation' => new Type\Enum(['default', 'throughout', 'forced'], [
                'allowEmpty' => false,
                'default' => 'default',
                'description' => t('Предварительный заказ'),
                'hint' => t('По-умолчанию означает: как в настройках модуля Магазин'),
                'ListFromArray' => [[
                    'default' => t('По умолчанию'),
                    'throughout' => t('Запрещено'),
                    'forced' => t('Только предзаказ')
                ]],
            ]),
            t('Налоги'),
            'tax_ids' => new Type\Varchar([
                'description' => t('Налоги'),
                'template' => '%shop%/productform/taxes.tpl',
                'default' => 'category',
                'list' => [['\Shop\Model\TaxApi', 'staticSelectList']]
            ]),
            t('Маркировка'),
            'marked_class' => (new Type\Varchar())
                ->setDescription(t('Класс маркируемых товаров'))
                ->setList('Shop\Model\Marking\MarkingApi::MarkedClassesSelectList')
                ->setDefault(''),
            'tn_ved_codes' => (new Type\Varchar())
                ->setDescription(t('Код ТН ВЭД. Если несколько, то через запятую'))
                ->setHint('Код ТН ВЭД. Состоит из 10 цифр без пробелов. Код применяется для товаров, которые ввозят на территорию Российской Федерации. Код определяется Единой товарной номенклатурой ВЭД и указан в международной транспортной накладной (CMR).'),
            'country_code' => (new Type\Varchar())
                ->setDescription(t('Страна производства'))
                ->setList(['Catalog\Model\Reference', 'getCountryCodes'], ['' => t('- Не выбрано -')])
                ->setMaxLength(2),
            'gtd' => (new Type\Varchar())
                ->setDescription(t('Номер Грузовой Таможенной Декларации'))
        ]);
    }

    /**
     * Добавляем раздел "Налоги" в категорию товара
     *
     * @param Dir $orm_dir - объект категории
     */
    public static function ormInitCatalogDir(Dir $orm_dir)
    {
        $orm_dir->getPropertyIterator()->append([
            t('Налоги'),
            'tax_ids' => new Type\Varchar([
                'description' => t('Налоги'),
                'default' => 'all',
                'template' => '%shop%/productform/taxes_dir.tpl',
                'list' => [['\Shop\Model\TaxApi', 'staticSelectList']],
                'rootVisible' => false,
            ]),
        ]);
    }

    /**
     * Расширяем объект User, добавляя в него доп свойства "Менеджер пользователя", "Минимальная сумма заказа"
     *
     * @param User $user
     */
    public static function ormInitUsersUser(User $user)
    {
        $user->getPropertyIterator()->append([
            t('Основные'),
            'manager_user_id' => (new Type\Integer())
                ->setIndex(true)
                ->setDescription(t('Менеджер пользователя'))
                ->setHint(t('У всех заказов пользователя будет автоматически указываться выбранный менеджер'))
                ->setList(['Shop\Model\OrderApi','getUsersManagersName'], [0 => t('- Не задан -')])
                ->setAllowEmpty(false),
            t('Настройка корзины'),
            'basket_min_limit' => (new Type\Decimal())
                ->setDescription(t('Минимальная сумма заказа (в базовой валюте)'))
                ->setMaxLength(20)
                ->setDecimal(2),
        ]);
    }

    /**
     * Расширяем объект UserGroup, добавляя в него доп свойство - "Минимальная сумма заказа"
     *
     * @param UserGroup $group
     */
    public static function ormInitUsersUserGroup(UserGroup $group)
    {
        $group->getPropertyIterator()->append([
            t('Настройка корзины'),
            'basket_min_limit' => (new Type\Decimal())
                ->setDescription(t('Минимальная сумма заказа (в базовой валюте)'))
                ->setMaxLength(20)
                ->setDecimal(2),
        ]);
    }

    /**
     * Добавляет в систему печатные формы для заказа
     *
     * @param array $list - массив установленных меню
     * @return array
     */
    public static function printFormGetList($list)
    {
        $list[] = new PrintForm\OrderForm();
        $list[] = new PrintForm\CommodityCheck();
        $list[] = new PrintForm\DeliveryNote();
        $list[] = new PrintForm\Torg12();
        $list[] = new PrintForm\Upd();
        $list[] = new PrintForm\SchetFactura();
        return $list;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items - массив установленных меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Магазин'),
            'alias' => 'orders',
            'link' => '%ADMINPATH%/shop-orderctrl/',
            'parent' => 0,
            'sortn' => 10,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Заказы'),
            'alias' => 'allorders',
            'link' => '%ADMINPATH%/shop-orderctrl/',
            'sortn' => 0,
            'typelink' => 'link',
            'parent' => 'orders'
        ];
        $items[] = [
            'title' => t('Предварительные заказы'),
            'alias' => 'advorders',
            'link' => '%ADMINPATH%/shop-reservationctrl/',
            'sortn' => 1,
            'typelink' => 'link',
            'parent' => 'orders'
        ];
        $items[] = [
            'title' => t('Скидочные купоны'),
            'alias' => 'discount',
            'link' => '%ADMINPATH%/shop-discountctrl/',
            'sortn' => 3,
            'typelink' => 'link',
            'parent' => 'orders'
        ];
        $items[] = [
            'title' => t('Доставка'),
            'alias' => 'deliverygroup',
            'link' => '%ADMINPATH%/shop-regionctrl/',
            'sortn' => 4,
            'typelink' => 'link',
            'parent' => 'orders'
        ];
        $items[] = [
            'title' => t('Способы доставки'),
            'alias' => 'delivery',
            'link' => '%ADMINPATH%/shop-deliveryctrl/',
            'parent' => 'deliverygroup',
            'typelink' => 'link',
            'sortn' => 1,

        ];
        $items[] = [
            'title' => t('Регионы доставки'),
            'alias' => 'regions',
            'link' => '%ADMINPATH%/shop-regionctrl/',
            'parent' => 'deliverygroup',
            'typelink' => 'link',
            'sortn' => 2,

        ];
        $items[] = [
            'title' => t('Зоны'),
            'alias' => 'zones',
            'link' => '%ADMINPATH%/shop-zonectrl/',
            'parent' => 'deliverygroup',
            'sortn' => 3,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Заказы на доставку'),
            'alias' => 'delivery_orders',
            'link' => '%ADMINPATH%/shop-deliveryorderctrl/',
            'parent' => 'deliverygroup',
            'typelink' => 'link',
            'sortn' => 4,
        ];
        $items[] = [
            'title' => t('Способы оплаты'),
            'alias' => 'payment',
            'link' => '%ADMINPATH%/shop-paymentctrl/',
            'sortn' => 5,
            'typelink' => 'link',
            'parent' => 'orders'
        ];
        $items[] = [
            'title' => t('Налоги'),
            'alias' => 'taxes',
            'link' => '%ADMINPATH%/shop-taxctrl/',
            'sortn' => 7,
            'typelink' => 'link',
            'parent' => 'orders'
        ];
        $items[] = [
            'title' => t('Транзакции'),
            'alias' => 'transactions',
            'parent' => 'userscontrol',
            'link' => '%ADMINPATH%/shop-transactionctrl/',
            'sortn' => 40,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Электронные чеки'),
            'alias' => 'receipt',
            'parent' => 'userscontrol',
            'link' => '%ADMINPATH%/shop-receiptsctrl/',
            'sortn' => 42,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Возвраты товаров'),
            'alias' => 'returns',
            'parent' => 'orders',
            'link' => '%ADMINPATH%/shop-returnsctrl/',
            'sortn' => 9,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Отгрузки заказов'),
            'alias' => 'shipments',
            'parent' => 'orders',
            'link' => '%ADMINPATH%/shop-ordershipmentctrl/',
            'sortn' => 10,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Сохранённые карты'),
            'alias' => 'saved_payment_methods',
            'parent' => 'userscontrol',
            'link' => '%ADMINPATH%/shop-savedpaymentmethodsctrl/',
            'sortn' => 60,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Бонусные карты'),
            'alias' => 'saved_bonus_cards',
            'parent' => 'userscontrol',
            'link' => '%ADMINPATH%/shop-bonuscardsctrl/',
            'sortn' => 61,
            'typelink' => 'link',
        ];
        return $items;
    }

    /**
     * Обработка событий по cron
     *
     * @param array $params - параметры cron
     * @return void
     * @throws DbException
     * @throws RSException
     */
    public static function cron($params)
    {
        //Запускаем в полночь проверку на автоматический перевод статусов заказов,
        //которые находятся в статусе L более N дней
        if (in_array(0, $params['minutes'])) {
            $sites = SiteManager::getSiteList();
            foreach ($sites as $site) {
                /** @var \Shop\Config\File $config */
                $config = ConfigLoader::byModule(__CLASS__, $site['id']);
                if ($config['auto_change_status'] && $config['auto_change_timeout_days'] && $config['auto_change_from_status']) {
                    self::autoChangeOrderStatus($config, $site['id']);
                }

                if ($config['auto_archive_orders'] && $config['auto_archive_orders_after_days'] > 0) {
                    $archive_api = new ArchiveOrderApi();
                    $archive_api->setSiteContext($site['id']);
                    echo t('Начинаем переносить заказы в архив')."\n";
                    $count = $archive_api->moveToArchiveOldOrders($config['auto_archive_orders_after_days']);
                    echo t('Перенесено в архив %0 заказов', [$count])."\n";
                }
            }
        }

        // Запускаем отправку уведомлений подписавшимся клиентам о поступлении товара
        foreach ($params['minutes'] as $minute) {
            // Задание запускается в 09:00, 12:00, 16:00
            if (in_array($minute, [540, 720, 960])) {
                $sites = SiteManager::getSiteList();
                foreach ($sites as $site) {
                    $config = ConfigLoader::byModule(__CLASS__, $site['id']);
                    if ($config['auto_send_supply_notice']) {
                        SiteManager::setCurrentSite($site);
                        ReservationApi::SendNoticeReceipt($site['id']);
                    }
                }
            }
        }

        //Проверим чеки раз в минуту, на случай если callback не отработал или него не существует впринципе.
        $sites = SiteManager::getSiteList();
        foreach ($sites as $site) {
            $config = ConfigLoader::byModule(__CLASS__, $site['id']);
            if ($config['cashregister_class'] && $config['cashregister_enable_auto_check']) { //Если только класс для касс задан и стоит флаг для проверки
                $api = new ReceiptApi();
                $api->checkWaitReceipts($site['id']);
            }

            //Обновляем раз в сутки список доступных хостов для ЧестногоЗнака
            if (in_array(180, $params['minutes']) //03:00
                && $config['true_mark_check_codes']
                && $config['true_mark_token'])
            {
                try {
                    $true_api = new CheckCodes($site['id']);
                    $true_api->getHosts(true);
                } catch (RSException $e) {}
            }
        }
    }

    /**
     * Автоматически переводит статус заказов, согласно настройкам модуля
     *
     * @param ShopConfig $config - объект конфига
     * @param int $site_id - id сайта
     */
    public static function autoChangeOrderStatus($config, $site_id)
    {
        $to_status = $config['auto_change_to_status'];
        $limit = 40;
        $offset = 0;

        $query = (new OrmRequest)
            ->select('id')
            ->from(new Order())
            ->where('dateofupdate < NOW() - INTERVAL #n DAY', [
                'n' => $config['auto_change_timeout_days']
            ])
            ->whereIn('status', $config['auto_change_from_status'])
            ->where([
                'site_id' => $site_id
            ]);

        $query = Manager::fire('shop.order.autoChangeStatus.before', [
            'query' => $query,
            'site_id' => $site_id,
            'auto_change_timeout_days' => $config['auto_change_timeout_days'],
            'auto_change_from_status' => $config['auto_change_from_status'],
            'auto_change_to_status' => $to_status
        ])->getResult()['query'];

        $ids = $query->exec()->fetchSelected(null, 'id');

        if ($ids) {
            $q = (new OrmRequest())
                ->from(new Order())
                ->whereIn('id', $ids)
                ->limit($limit);

            /** @var Order[] $orders */
            while ($orders = $q->offset($offset)->objects()) {
                foreach ($orders as $order) {

                    echo t('Автоматически меняем статус заказа ID:%0', [$order['id']]) . "\n";

                    $order['status'] = $to_status;
                    $order['is_exported'] = 0;
                    $order->update();

                    //Отправляем уведомление при автосмене статуса
                    $notice = new \Shop\Model\Notice\AutoChangeStatus();
                    $notice->init($order);
                    AlertsManager::send($notice);
                }
                $offset += $limit;
            }
        }
    }
}