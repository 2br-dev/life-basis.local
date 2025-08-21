<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Config;
use Banners\Model\Orm\Banner;
use Main\Model\NoticeSystem\InternalAlerts;
use MobileSiteApp\Model\AppApi;
use MobileSiteApp\Model\AppTypes\MobileSiteApp;
use MobileSiteApp\Model\Behavior\BannersBanner;
use RS\Application\Application;
use RS\Config\Loader;
use \RS\Orm\Type;
use RS\Router\Route;

/**
 * Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
 */
class Handlers extends \RS\Event\HandlerAbstract
{
    const
        BANNER_TYPE_NONE = '0',
        BANNER_TYPE_LINK = 'Link',
        BANNER_TYPE_MENU = 'Menu',
        BANNER_TYPE_PRODUCT = 'Product',
        BANNER_TYPE_CATEGORY = 'Category';

    function init()
    {
        $this
            ->bind('getapps')
            ->bind('mobilesiteapp.gettemplates')
            ->bind('orm.afterwrite.shop-order')
            ->bind('orm.init.banners-banner')
            ->bind('orm.init.menu-menu')
            ->bind('orm.init.article-category')
            ->bind('orm.init.catalog-dir')
            ->bind('orm.init.shop-delivery')
            ->bind('orm.init.shop-payment')
            ->bind('getroute')
            ->bind('internalalerts.get')
            ->bind('getmenus')
            ->bind('main.getmanifestinfo')
            ->bind('start')
            ->bind('shop.creatorPlatforms.getList')
            ->bind('initialize');
    }

    /**
     * Расширяем объект баннера
     */
    public static function initialize()
    {
        Banner::attachClassBehavior(new BannersBanner());
    }

    /**
     * Расширяем объект баннера
     *
     * @param \Banners\Model\Orm\Banner $banner - объект баннера
     */
    public static function ormInitBannersBanner(\Banners\Model\Orm\Banner $banner)
    {
        $banner->getPropertyIterator()->append([
            t('Мобильное приложение'),
            '_mobile_banner_type_' => new Type\Varchar([
                'description' => t('Тип баннера'),
                'template' => '%mobilesiteapp%/form/banner/type.tpl',
                'runtime' => true
            ]),
            'mobile_banner_type' => new Type\Varchar([
                'description' => t('Тип баннера'),
                'listFromArray' => [[
                    self::BANNER_TYPE_NONE => t('Не выбрано'),
                    self::BANNER_TYPE_LINK => t('Ссылка'),
                    self::BANNER_TYPE_MENU => t('Переход на страницу меню'),
                    self::BANNER_TYPE_PRODUCT => t('Переход на товар'),
                    self::BANNER_TYPE_CATEGORY => t('Переход в директорию')
                ]],
                'default' => "0",
                'visible' => false
            ]),
            'mobile_link' => new Type\Varchar([
                'description' => t('Страницы для показа пользователю'),
                'template' => '%mobilesiteapp%/form/pushtokenmessage/link.tpl',
                'default' => "",
                'visible' => false
            ]),
            'mobile_menu_id' => new Type\Integer([
                'description' => t('Страницы для показа пользователю'),
                'template' => '%mobilesiteapp%/form/pushtokenmessage/menu_id.tpl',
                'default' => "0",
                'visible' => false
            ]),
            'mobile_product_id' => new Type\Integer([
                'description' => t('Товар для показа пользователю'),
                'template' => '%mobilesiteapp%/form/pushtokenmessage/product_id.tpl',
                'default' => "0",
                'visible' => false
            ]),
            'mobile_category_id' => new Type\Integer([
                'description' => t('Категория для показа пользователю'),
                'template' => '%mobilesiteapp%/form/pushtokenmessage/category_id.tpl',
                'default' => "0",
                'visible' => false
            ])
        ]);
    }

    /**
     * Добавляем в manifest.json сведения о мобильном приложении
     * @param $params
     * @return
     */
    public static function mainGetManifestInfo($params)
    {
        $config = Loader::byModule(__CLASS__);
        $router = \RS\Router\Manager::obj();

        if ($config->enable_app_sticker && !$router->isAdminZone()) {
            $site    = \RS\Site\Manager::getSite();
            if (method_exists($site, 'getMainDomain')) { //Для совместимости со старыми версиями RS
                $domain = $site->getMainDomain() . $site->getRootUrl();
                $app_api = new AppApi();
                $data = $app_api->getSubscribeInfo($domain);

                if (!empty($data['googleplay_app_id']) && isset($data['google_icon'])
                    && strpos($data['google_icon'], 'nophoto') === false) {

                    $params['data']['prefer_related_applications'] = true;
                    $params['data']['related_applications'] = [
                        [
                            "platform" => "play",
                            "id" => $data['googleplay_app_id']
                        ]
                    ];
                }
            }
        }

        return $params;
    }


    /**
     * Действия на старте системы
     */
    public static function start()
    {
        $config = Loader::byModule(__CLASS__);
        $router = \RS\Router\Manager::obj();

        if ($config->enable_app_sticker && !$router->isAdminZone()) {
            //Размещаем стикеры о наличии мобильного приложения для сайта
            $app     = Application::getInstance();
            $site    = \RS\Site\Manager::getSite();
            if (method_exists($site, 'getMainDomain')) { //Для совместимости со старыми версиями RS
                $domain = $site->getMainDomain() . $site->getRootUrl();
                $app_api = new AppApi();
                $data = $app_api->getSubscribeInfo($domain);

                if (isset($data['appstore_app_id']) && $data['appstore_app_id']) {
                    //Для Apple
                    $app->meta->add([
                        'name' => 'apple-itunes-app',
                        'content' => 'app-id=' . $data['appstore_app_id'],
                        'data-title' => $site['title']
                    ]);
                }

                if (isset($data['googleplay_app_id']) && isset($data['google_icon'])
                    && strpos($data['google_icon'], 'nophoto') === false) {
                    //Для Android
                    $app->addCss($data['google_icon'], null, BP_ROOT, true, [
                        'rel' => 'apple-touch-icon'
                    ]);

                    $app->meta->add([
                        'name' => 'google-play-app',
                        'content' => 'app-id=' . $data['googleplay_app_id'],
                        'data-title' => $site['title']
                    ]);
                }
            }
        }
    }

    /**
     * Возвращает тип приложения для доступа к внешнему API
     *
     * @param array $app_types - массив из уже существующих приложений
     * @return array
     */
    public static function getApps($app_types)
    {
        $app_types[] = new \MobileSiteApp\Model\AppTypes\MobileSiteApp();
        return $app_types;
    }

    /**
     * Добавляет пункты меню в административной панели
     * @param array $items Пункты меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Мобильное приложение'),
            'alias' => 'mobilesiteapp',
            'link' => '%ADMINPATH%/mobilesiteapp-appctrl/',
            'parent' => 'modules',
            'typelink' => 'link',
        ];

        return $items;
    }


    /**
     * Возвращает список из путей к шаблонам. Путь может быть отсительным с использованием %наименование модуля%. Принимается массив
     * ключ - путь шаблону
     * значение - массив со сведениями о данные
     *
     *
     * Например:
     * mobilesiteapp => array( //Модуль мобильного приложения
     *   'title' => 'По умолчанию', //Наименование шаблона
     *   'css'   => 'build/css/', //Относительный путь к css файлам
     *   'fonts' => 'build/fonts/', //Относительный путь к css файлам
     *   'js'    => 'build/js/', //Относительный путь к js файлам
     * )
     *
     * @param array $templates - массив с шаблонами собраные из модулей
     * @return array
     */
    public static function mobileSiteAppGetTemplates($templates)
    {
        $templates['mobilesiteapp'] = [
            'title'          => t('По умолчанию'),                     //Наименование шаблона
            'mobile_root'    => '%MOBILEPATH%/appsource',              //Относительный путь к файлам. %MOBILEPATH% - путь к приложению
            'templates_root' => '%MOBILEPATH%/view',                   //Относительный путь к шаблонам. %MOBILEPATH% - путь к приложению
            'www_dir'        => '%MOBILEROOT%/www',                    //Относительный путь к css файлам. %MOBILEROOT% - путь к корню приложения
            'css'            => '%MOBILEROOT%/www/build',              //Относительный путь к css файлам. %MOBILEROOT% - путь к корню приложения
            'fonts'          => '%MOBILEROOT%/www/build/assets/fonts', //Относительный путь к файлам с шрифтами. %MOBILEROOT% - путь к корню приложения
            'js'             => '%MOBILEROOT%/www/build',              //Относительный путь к js файлам. %MOBILEROOT% - путь к корню приложения
            'img'            => '%MOBILEROOT%/www/images',             //Относительный путь к картинкам. %MOBILEROOT% - путь к корню приложения
        ];
        return $templates;
    }


    /**
     * Расширяем объект меню
     *
     * @param \Menu\Model\Orm\Menu $menu - объект меню
     */
    public static function ormInitMenuMenu(\Menu\Model\Orm\Menu $menu)
    {
        $menu->getPropertyIterator()->append([
            t('Мобильное приложение'),
            'mobile_public' => new Type\Integer([
                'maxLength' => '1',
                'default' => 0,
                'description' => t('Показывать в мобильном приложении'),
                'hint' => t('Необходимо наличие мобильного приложения. См. документацию.'),
                'CheckboxView' => [1,0],
                'meVisible' => false,
                'specVisible' => false,
            ]),
            'mobile_image' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Идентификатор картинки Ionic'),
                'template' => '%mobilesiteapp%/form/menu/mobile_image.tpl',
                'hint' => t('Укажите в данном поле идентификатор картинки из справочника'),
                'meVisible' => false,
                'appVisible' => true,
            ]),
        ]);
    }

    /**
     * Расширяем объект статьи
     *
     * @param \Article\Model\Orm\Category $category - объект меню
     */
    public static function ormInitArticleCategory(\Article\Model\Orm\Category $category)
    {
        $category->getPropertyIterator()->append([
            t('Мобильное приложение'),
            'mobile_public' => new Type\Integer([
                'maxLength' => '1',
                'default' => 0,
                'description' => t('Показывать в мобильном приложении'),
                'hint' => t('Необходимо наличие мобильного приложения. См. документацию.'),
                'CheckboxView' => [1,0],
                'meVisible' => false,
                'specVisible' => false,
            ]),
            'mobile_image' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Идентификатор картинки Ionic'),
                'template' => '%mobilesiteapp%/form/menu/mobile_image.tpl',
                'hint' => t('Укажите в данном поле идентификатор картинки из справочника'),
                'meVisible' => false,
                'specVisible' => false,
            ]),
        ]);
    }


    /**
     * Расширяем объект категории
     *
     * @param \Catalog\Model\Orm\Dir $dir - объект категории
     */
    public static function ormInitCatalogDir(\Catalog\Model\Orm\Dir $dir)
    {
        $dir->getPropertyIterator()->append([
            t('Мобильное приложение'),
            'mobile_background_color' => new Type\Color([
                'description' => t('Основной цвет фона категории'),
                'hint' => 'Будет использовать, если не установлено "Изображение категории"',
                'maxLength' => '11',
                'default' => '#F7F7F7',
                'appVisible' => true,
                'rootVisible' => false,
            ]),
            'mobile_background_icon_color' => new Type\Color([
                'description' => t('Вторичный цвет фона категории'),
                'hint' => 'Будет использовать, если не установлено "Изображение категории"',
                'maxLength' => '11',
                'default' => '#EAEAEA',
                'appVisible' => true,
                'rootVisible' => false,
            ]),
            'mobile_background_title_color' => new Type\Color([
                'description' => t('Цвет названия категории'),
                'hint' => 'Будет использовать, если не установлено "Изображение категории"',
                'maxLength' => '11',
                'default' => '#1B1B1F',
                'appVisible' => true,
                'rootVisible' => false,
            ]),
            'mobile_tablet_icon' => new Type\Image([
                'description' => t('Иконка'),
                'hint' => 'Будет использовать, если не установлено "Изображение категории"',
                'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'], //Допустимы форматы jpg, png, gif
                'appVisible' => true,
                'rootVisible' => false,
            ]),
            'mobile_tablet_background_image' => new Type\Image([
                'description' => t('Изображение категории'),
                'hint' => 'Перекрывает остальные настройки категории для приложения. Оптимальный размер 1244x555 px',
                'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'], //Допустимы форматы jpg, png, gif
                'appVisible' => true,
                'rootVisible' => false,
            ]),

        ]);
    }

    /**
     * Расширяем объект доставки
     *
     * @param \Shop\Model\Orm\Delivery $delivery - объект доставки
     */
    public static function ormInitShopDelivery(\Shop\Model\Orm\Delivery $delivery)
    {
        $delivery->getPropertyIterator()->append([
            t('Мобильное приложение'),
            'mobilesiteapp_additional_html' => new Type\MixedType([
                'description' => t('Дополнительный функционал для приложения на Ionic'),
                'visible' => false,
                'appVisible' => true,
            ]),
            'mobilesiteapp_description' => new Type\Richtext([
                'description' => t('Описание'),
            ]),
            'mobilesiteapp_hide' => new Type\Integer([
                'description' => t('Скрыть в мобильном приложении'),
                'checkboxView' => [1, 0],
                'default' => 0,
            ])
        ]);
    }

    /**
     * Расширяем объект оплаты
     *
     * @param \Shop\Model\Orm\Payment $payment - объект оплаты
     */
    public static function ormInitShopPayment(\Shop\Model\Orm\Payment $payment)
    {
        $payment->getPropertyIterator()->append([
            t('Мобильное приложение'),
                'mobilesiteapp_hide' => new Type\Integer([
                    'description' => t('Скрыть в мобильном приложении'),
                    'checkboxView' => [1, 0],
                    'default' => 0,
                ])
        ]);
    }


    /**
     * Возвращает присок доступных маршрутов
     *
     * @param Route[] $routes
     * @return Route[]
     */
    public static function getRoute($routes)
    {
        //Мобильный сайт
        $routes[] = new Route('mobilesiteapp-front-gate', [
            '/mobilesiteapp/{Act}/',
            '/mobilesiteapp/'
        ], null, t('Мобильный сайт'));

        $routes[] = new Route('mobilesiteapp-front-appinstall',
            '/app-install/', [], t('Страница установки приложения'));

        return $routes;
    }

    /**
     * Обработка события создания или обновления заказа, отсылка PUSH уведомления об изменениях
     *
     * @param array $data - массив данных
     */
    public static function ormAfterwriteShopOrder($data)
    {
        if (Loader::byModule(__CLASS__)->push_enable
            && $data['flag'] == \RS\Orm\AbstractObject::UPDATE_FLAG
            && $data['orm']['notify_user']
            && $data['orm']->canUserNotify()) //Если заказ обновился и нужно уведомить пользователя
        {
            $push = new \MobileSiteApp\Model\Push\OrderChangeToUser();
            $push->init($data['orm']);
            $push->send();
        }
    }

    /**
     * Проверяет если вдруг истек срок подписки, то на спец странице будет специальное сообщение
     *
     * @param array $params - параметры
     */
    public static function internalAlertsGet($params)
    {
        $internal_alerts = $params['internal_alerts'];
        $app_api = new AppApi();

        /**
         * @var $sites \Site\Model\Orm\Site[]
         */
        $sites = \RS\Site\Manager::getSiteList();
        foreach($sites as $site) {
            if (method_exists($site, 'getMainDomain')) {  //Для совместимости со старыми версиями RS
                $domain = $site->getMainDomain().$site->getRootUrl();
                if ($text = $app_api->getExpireText($domain)) {
                    $href = $app_api->getControlUrl($domain);
                    $internal_alerts->addMessage($text, $href, null, InternalAlerts::STATUS_WARNING);
                }
            }
        }
    }

    /**
     * Привносим в систему идентификатор создателя заказа
     *
     * @param array $list
     * @return array
     */
    public static function shopCreatorPlatformsGetList($list)
    {
        $list[MobileSiteApp::CREATOR_PLATFORM_ID] = t('Мобильное приложение');
        return $list;
    }
}