<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Config;
use Catalog\Model\Orm\OneClickItem;
use Crm\Model\Autotask\AbstractIfRule;
use Crm\Model\AutoTask\RuleIf\CreateFeedback;
use Crm\Model\Autotask\Ruleif\CreateOneClick;
use Crm\Model\Autotask\Ruleif\CreateOrder;
use Crm\Model\Autotask\Ruleif\CreateReservation;
use Crm\Model\Autotask\RuleIfSchedule;
use Crm\Model\Autotask\RuleIfTask;
use Crm\Model\Autotask\RuleIfTaskList;
use Crm\Model\Autotask\RuleThenTask;
use Crm\Model\AutoTaskApi;
use Crm\Model\AutoTaskRuleApi;
use Crm\Model\Behavior\UsersUser;
use Crm\Model\Board\DealBoardItem;
use Crm\Model\Board\TaskBoardItem;
use Crm\Model\Links\LinkManager;
use Crm\Model\Links\Type\LinkTypeOneClickItem;
use Crm\Model\Links\Type\LinkTypeOrder;
use Crm\Model\Links\Type\LinkTypeReservation;
use Crm\Model\Links\Type\LinkTypeUser;
use Crm\Model\Log\LogTelephony;
use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\TaskApi;
use Crm\Model\Telephony\Manager as TelephonyManager;
use Crm\Model\Telephony\Provider\Second\SecondProvider;
use Crm\Model\Telephony\Provider\Telphin\TelphinProvider;
use Main\Model\Comet\LongPolling;
use RS\Application\Application;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Log\AbstractLog;
use RS\Orm\AbstractObject;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Reservation;
use Users\Model\Orm\User;

/**
* Класс содержит обработчики событий, на которые подписан модуль
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    /**
    * Добавляет подписку на события
    * 
    * @return void
    */
    function init()
    {
        $this
            ->bind('orm.init.users-user')
            ->bind('orm.init.catalog-oneclickitem')
            ->bind('orm.init.shop-reservation')
            ->bind('controller.beforeexec.shop-admin-orderctrl')
            ->bind('controller.beforeexec.users-admin-ctrl')
            ->bind('orm.afterwrite.shop-order')
            ->bind('orm.afterwrite.catalog-oneclickitem')
            ->bind('orm.afterwrite.shop-reservation')
            ->bind('orm.afterwrite.feedback-resultitem')
            ->bind('orm.afterwrite.crm-task')
            ->bind('crm.getboardtypes')
            ->bind('crm.telephony.getproviders')
            ->bind('crm.autotask.getifrules')
            ->bind('crm.autotask.getthenrules')
            ->bind('cron')
            ->bind('getlogs')
            ->bind('getmenus') //событие сбора пунктов меню для административной панели
            ->bind('getroute')
            ->bind('start')
            ->bind('initialize');
    }

    /**
     * Регистрируем классы условий для автозадач
     *
     * @param $rules
     * @return []
     */
    public static function crmAutoTaskGetIfRules($rules)
    {
        $rules[] = new RuleIfTask();
        $rules[] = new RuleIfSchedule();
        $rules[] = new RuleIfTaskList();

        return $rules;
    }

    /**
     * Регистрируем классы действий для автозадач
     *
     * @param $rules
     * @return []
     */
    public static function crmAutoTaskGetThenRules($rules)
    {
        $rules[] = new RuleThenTask();

        return $rules;
    }

    /**
     * Возвращает классы логирования этого модуля
     *
     * @param AbstractLog[] $list - список классов логирования
     * @return AbstractLog[]
     */
    public static function getLogs($list)
    {
        $list[] = LogTelephony::getInstance();
        return $list;
    }

    /**
     * Обрабатывает параметр from_call при создании заказа
     *
     * @param $params
     * @return void
     */
    public static function controllerBeforeExecShopAdminOrderCtrl($params)
    {
        if ($params['action'] == 'add') {
            $controller = $params['controller'];

            $from_call = $controller->url->get('from_call', TYPE_INTEGER);
            $call = new CallHistory($from_call);
            if ($call['id']) {
                $order = $controller->getApi()->getElement();
                $user = $call->getOtherUser();

                if ($user['id'] > 0) {
                    $order['user_type'] = Order::USER_TYPE_USER;
                    $order['user_id'] = $user['id'];
                } else {
                    $order['user_type'] = Order::USER_TYPE_NOREGISTER;
                    $order['user_phone'] = $user->phone;
                }

            }
        }
    }

    /**
     * Обрабатывает параметр from_call при создании пользователя
     *
     * @param array $params
     * @return void
     */
    public static function controllerBeforeexecUsersAdminCtrl($params)
    {
        if ($params['action'] == 'add') {
            $controller = $params['controller'];

            $from_call = $controller->url->get('from_call', TYPE_INTEGER);
            $call = new CallHistory($from_call);
            if ($call['id']) {
                $call_user = $call->getOtherUser();
                $user = $controller->getApi()->getElement();
                $user['phone'] = $call_user['phone'];
            }
        }
    }

    /**
     * Расширяем объекты из других модулей
     */
    public static function initialize()
    {
        User::attachClassBehavior(new UsersUser());
    }

    /**
     *  Обработчик открытия страницы
     */
    public static function start()
    {
        $config = Loader::byModule(__CLASS__);

        if ($config['tel_enable_call_notification']) {
            $app = Application::getInstance();
            $app->addJsVar('telephonyOffsetBottom', $config['tel_bottom_offset_px']);

            $providers = TelephonyManager::getProviders();
            $user_id = Auth::getCurrentUser()->id;
            if ($user_id > 0) {
                foreach ($providers as $provider) {
                    if ($provider->getExtensionIdByUserId($user_id)) {
                        $messages = TelephonyManager::getCurrentUserMessages($user_id);
                        if ($messages) {
                            $app->addJsVar('currentTelephonyMessages', $messages);
                        }

                        //Инициализируем COMET для IP телефонии, если для текущего администратора настроен добавочный номер
                        LongPolling::getInstance()->enable();
                        break;
                    }
                }
            }
        }
    }

    /**
     * Добавляет маршруты в систему
     *
     * @param $routes
     */
    public static function getRoute($routes)
    {
        $routes[] = new \RS\Router\Route('crm-front-telephonyevents',
            [
                '/telephony/{provider}-{secret}/'
            ], null, 'Шлюз событий телефонии', true);

        return $routes;
    }

    /**
     * Обработчик события планировщика
     *
     * @param array @params
     */
    public static function cron($params)
    {
        $task_api = new TaskApi();
        foreach($params['minutes'] as $minute) {
            if (($minute % 2) == 0) { //Раз в 2 минуты проверять

                $count = $task_api->sendTaskNotice();

                echo t('Отправлено %0 сообщений о скором окончании срока выполнения задач', [$count]);

                break; //Выполняем только один раз
            }
        }
        AutoTaskApi::run($params, null, AbstractIfRule::MODE_CRON);
    }

    /**
     * Обработчик вызывается при создании задачи
     *
     * @param array $params
     */
    public static function ormAfterWriteCrmTask($params, $event)
    {
        AutoTaskApi::run($params, $event);
    }

    /**
     * Обработчик вызывается при ответе в форме обратной связи
     *
     * @param array $params
     */
    public static function ormAfterWriteFeedbackResultItem($params, $event)
    {
        AutoTaskApi::run($params, $event);

        $feedback = $params['orm'];
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {

            //Создаем автозадачи, если необходимо
            $event = new CreateFeedback();
            $event->init($feedback);

            AutoTaskRuleApi::run($event);
        }

    }

    /**
     * Привязываем CRM сущности к создаваемому заказу
     *
     * @param $params
     * @throws \RS\Exception
     */
    public static function ormAfterWriteShopOrder($params, $event)
    {
        AutoTaskApi::run($params, $event);
        $order = $params['orm'];
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {
            if ($order['_tmpid']<0){
                //Обновляем ID у всех объектов CRM, привязанных к заказу
                LinkManager::updateLinkId($order['_tmpid'], $order['id'], LinkTypeOrder::getId());
            }

            //Создаем автозадачи, если необходимо
            $event = new CreateOrder();
            $event->init($order);

            AutoTaskRuleApi::run($event);
            CallHistory::autoUpdateProcessedFlag();
        }
    }

    /**
     * Обработчик вызывается при создании покупки в 1 клик
     *
     * @param array $params
     */
    public static function ormAfterwriteCatalogOneClickItem($params, $event)
    {
        AutoTaskApi::run($params, $event);

        $oneclick = $params['orm'];
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {
            //Создаем автозадачи, если необходимо
            $event = new CreateOneClick();
            $event->init($oneclick);

            AutoTaskRuleApi::run($event);
        }
    }

    /**
     * Обработчик вызывается при создании предзаказа
     *
     * @param array $params
     */
    public static function ormAfterwriteShopReservation($params, $event)
    {
        AutoTaskApi::run($params, $event);

        $reservation = $params['orm'];
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {
            //Создаем автозадачи, если необходимо
            $event = new CreateReservation();
            $event->init($reservation);

            AutoTaskRuleApi::run($event);
        }

    }

    /**
     * Добавляет покупке в 1 клик блок CRM
     *
     * @param Reservation $reservation
     * @throws \RS\Exception
     */
    public static function ormInitShopReservation(Reservation $reservation)
    {
        $reservation->getPropertyIterator()->append([
            t('Сделки'),
            '__deal__' => new \Crm\Model\OrmType\DealBlock([
                'linkType' => LinkTypeReservation::getId(),
                'onlyExists' => true
            ])
        ]);
    }

    /**
     * Добавляет покупке в 1 клик блок CRM
     *
     * @param OneClickItem $one_click_item
     * @throws \RS\Exception
     */
    public static function ormInitCatalogOneClickItem(OneClickItem $one_click_item)
    {
        $one_click_item->getPropertyIterator()->append([
            t('Сделки'),
            '__deal__' => new \Crm\Model\OrmType\DealBlock([
                'linkType' => LinkTypeOneClickItem::getId(),
                'onlyExists' => true
            ])
        ]);
    }

    /**
     * Добавляет вкладку взаимодействия у клиента
     *
     * @param User $user
     * @throws \RS\Exception
     */
    public static function ormInitUsersUser(User $user)
    {
        $user->getPropertyIterator()->append([
            t('Взаимодействия'),
            '__interaction__' => new \Crm\Model\OrmType\InteractionBlock([
                'linkType' => LinkTypeUser::getId(),
                'onlyExists' => true
            ]),
        ]);
    }

    /**
    * Возвращает пункты меню этого модуля в виде массива
    *
    * @param array $items - массив с пунктами меню
    * @return array
    */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => 'CRM',
            'alias' => 'crm',
            'link' => '%ADMINPATH%/crm-dealctrl/',
            'parent' => 0,
            'typelink' => 'link',
        ];

        $items[] = [
            'title' => t('Сделки'),
            'alias' => 'crm-deal',
            'link' => '%ADMINPATH%/crm-dealctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 10
        ];

        $items[] = [
            'title' => t('Взаимодействия'),
            'alias' => 'crm-interaction',
            'link' => '%ADMINPATH%/crm-interactionctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 20
        ];

        $items[] = [
            'title' => t('Задачи'),
            'alias' => 'crm-task',
            'link' => '%ADMINPATH%/crm-taskctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 30
        ];

        $items[] = [
            'title' => t('Kanban доска'),
            'alias' => 'crm-board',
            'link' => '%ADMINPATH%/crm-boardctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 50
        ];

        $items[] = [
            'title' => t('Диаграмма Ганта'),
            'alias' => 'crm-taskgantctrl',
            'link' => '%ADMINPATH%/crm-taskgantctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 55
        ];

        $items[] = [
            'title' => t('Звонки'),
            'alias' => 'crm-call-history',
            'link' => '%ADMINPATH%/crm-callhistoryctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 60
        ];

        $items[] = [
            'title' => t('Автоматизация'),
            'alias' => 'crm-auto',
            'link' => '%ADMINPATH%/crm-autotaskctrl/',
            'parent' => 'crm',
            'typelink' => 'link',
            'sortn' => 60
        ];

        $items[] = [
            'title' => t('Типы задач'),
            'alias' => 'crm-tasktype',
            'link' => '%ADMINPATH%/crm-tasktypectrl/',
            'parent' => 'crm-auto',
            'typelink' => 'link',
            'sortn' => 35
        ];

        $items[] = [
            'title' => t('Автозадачи'),
            'alias' => 'crm-autotask',
            'link' => '%ADMINPATH%/crm-autotaskctrl/',
            'parent' => 'crm-auto',
            'typelink' => 'link',
            'sortn' => 36
        ];

        return $items;
    }

    /**
     * Регистрирует типы объектов, отображаемых на доске Kanban
     *
     * @param $list
     * @return array
     */
    public static function crmGetBoardTypes($list)
    {
        $list[] = new TaskBoardItem();
        $list[] = new DealBoardItem();

        return $list;
    }

    /**
     * Регистрирует провайдеры телефонии, которые идут вместе с модулем CRM
     *
     * @param $list
     * @return array
     */
    public static function crmTelephonyGetProviders($list)
    {
        $list[] = new TelphinProvider();

        return $list;
    }
}