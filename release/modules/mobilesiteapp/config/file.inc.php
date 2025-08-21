<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace MobileSiteApp\Config;

use Catalog\Model\DirApi;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Module\Exception as ModuleException;
use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
* Конфигурационный файл модуля Мобильный сайт приложение
*/
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'mobilesiteapp_version' => new Type\Varchar([
                    'description' => t('Версия мобильного приложения'),
                    'listFromArray' => [[
                        'v1' => 'Версия 1',
                        'v2' => 'Версия 2',
                    ]],
                    'default' => 'v1',
                    'visible' => false
                ]),
                'allow_user_groups' => new Type\ArrayList([
                    'runtime' => false,            
                    'description' => t('Группы пользователей, для которых доступно данное приложение'),
                    'list' => [['\Users\Model\GroupApi','staticSelectList']],
                    'size' => 7,
                    'attr' => [['multiple' => true]]
                ]),
                'disable_buy' => new Type\Integer([
                    'description' => t('Скрыть корзину в приложении'),
                    'checkboxView' => [1,0]
                ]),
                'push_enable' => new Type\Integer([
                    'description' => t('Включить Push уведомления для данного приложения'),
                    'checkboxView' => [1,0]
                ]),
                'banner_zone' => new Type\Integer([
                    'description' => t('Баннерная зона'),
                    'list' => [['\Banners\Model\ZoneApi','staticAdminSelectList']],
                    'hint' => t('Баннерная зона, из которой будут выводиться баннеры на главной странице мобильного приложения')
                ]),
                'mobile_phone' => new Type\Varchar([
                    'description' => t('Номер телефона для отображения в приложении'),
                    'hint' => t('Если пусто, то отображаться на будет'),
                    'default' => '+7(000)000-00-00'
                ]),
                'root_dir' => new Type\Integer([
                    'description' => t('Корневая директория'),
                    'tree' => [['\Catalog\Model\DirApi','staticTreeList'], 0, [0 => t('- Корень категория -')]],
                    'hint' => t('На главной странице приложения будут отображены категории, являющиеся дочерними для указанной в данной опции')
                ]),
                'enable_app_sticker' => new Type\Varchar([
                    'description' => t('Отображать стикер о том, что есть приложение у сайта'),
                    'checkboxView' => [1,0]
                ]),
            t('Версия 1'),
                'default_theme' => new Type\Varchar([
                    'runtime' => false,
                    'description' => t('Шаблон по умолчанию'),
                    'list' => [['\MobileSiteApp\Model\TemplateManager','staticTemplatesList']],
                ]),
                'tablet_root_dir_sizes' => new Type\Varchar([
                    'description' => t('Размеры отображения категорий для главной на планшете'),
                    'hint' => t('M - middle, s - small. Категории будут отображаться последовательно согласно схеме'),
                ]),
                'products_pagesize' => new Type\Integer([
                    'description' => t('По сколько товаров показывать в категории'),
                    'hint' => t('Отвечает за количество подгружаемых единоразово товаров, остальные товары будут загружаться при прокрутке до последнего товара в списке')
                ]),
                'menu_root_dir' => new Type\Integer([
                    'description' => t('Корневой элемент для меню'),
                    'tree' => [['\Menu\Model\Api','staticTreeList'], 0, [0 => t('- Верхний уровень -')]],
                    'hint' => t('В мобильном приложении будут отображены дочерние к выбранному здесь пункту меню')
                ]),
                'top_products_dir' => new Type\Integer([
                    'description' => t('Категория топ товаров'),
                    'tree' => [['\Catalog\Model\DirApi','staticSpecTreeList'], 0, ['' => t('- Не выбрана -')]],
                    'hint' => t('Указывает категорию, из которой выбирать товары для отображения на главной активности приложения')
                ]),
                'top_products_pagesize' => new Type\Integer([
                    'description' => t('Сколько товаров показывать в топе'),
                ]),
                'top_products_order' => new Type\Varchar([
                    'description' => t('Поле сортировки топ товаров'),
                    'listFromArray' => [[
                        'id' => 'ID',
                        'title' => t('Название'),

                        'num DESC' => t('По наличию'),
                        'id DESC' => t('ID обратн. порядок'),
                        'dateof DESC' => t('По новизне'),
                        'rating DESC' => t('По рейтингу'),
                    ]]
                ]),
                'mobile_products_size' => new Type\Integer([
                    'description' => t('Сколько товаров показывать на мобильном устройстве'),
                    'listFromArray' => [[
                        '12' => '1',
                        '6' => '2',
                        '4' => '3',
                        '3' => '4',
                    ]]
                ]),
                'tablet_products_size' => new Type\Integer([
                    'description' => t('Сколько товаров показывать на планшете'),
                    'listFromArray' => [[
                        '12' => '1',
                        '6' => '2',
                        '4' => '3',
                        '3' => '4',
                    ]]
                ]),
                'article_root_category' => new Type\Integer([
                    'description' => t('Корневой элемент новостей'),
                    'hint' => t('С какой категории выводить'),
                    'tree' => [['\Article\Model\CatApi', 'staticTreeList'], 0, [0 => t('- Верхний уровень -')]],
                ]),
            t('Версия 2'),
                'enable_discount_coupons' => new Type\Varchar([
                    'description' => t('Давать возможность указать купон на скидку в корзине'),
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'search_history_length' => new Type\Integer([
                    'description' => t('Количество записей в поисковой истории'),
                    'default' => 10,
                ]),
                'captcha' => new Type\Varchar([
                    'description' => t('Капча в приложении при регистрации'),
                    'listFromArray' => [[
                        'none' => 'Отключена',
                        'system' => 'Из настроек системы',
                        'RS-default' => 'ReadyScript "Стандарт"',
                    ]],
                    'default' => 'system'
                ]),
                'promo_menu' => new Type\ArrayList([
                    'description' => t('Промо пункты меню'),
                    'hint' => t('Выберите пункты меню, которые будет выводиться блоке на главной'),
                    'tree' => [['\Menu\Model\Api', 'staticTreeList'], 0, ['hide' => t('Не отображать')]],
                    'runtime' => false,
                    'attr' => [[
                        AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                    ]]
                ]),
                'promo_special_dirs' => new Type\ArrayList([
                    'description' => t('Категории для спец.блока'),
                    'hint' => t('Выберите категории, которые будут выводиться в спец.блоке с быстрыми кнопками на главной'),
                    'tree' => [['\Catalog\Model\DirApi', 'staticTreeList']],
                    'runtime' => false,
                    'attr' => [[
                        AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                    ]]
                ]),
                'info_block_menu' => new Type\Varchar([
                    'description' => t('Корневой пункт меню для блока "Информация" в личном кабинете'),
                    'hint' => t('Выберите корневой пункт меню, из которого будет выводиться в блоке "Информация" в личном кабинете'),
                    'tree' => [['\Menu\Model\Api', 'staticTreeList'], 0, ['hide' => t('Не отображать')]],
                ]),
                'payment_target' => new Type\Varchar([
                    'description' => t('Открывать оплату'),
                    'listFromArray' => [[
                        '_self' => 'Внутри приложения',
                        '_system' => 'Во внешнем браузере',
                    ]],
                    'default' => '_system'
                ]),
                'product_warehouses' => new Type\Varchar([
                    'description' => t('Показывать наличие складов в карточке товара'),
                    'listFromArray' => [[
                        'hide' => 'Не отображать',
                        'all' => 'Все доступные склады',
                        'only_in_stock' => 'Только доступные склады с наличием',
                    ]],
                    'default' => 'all'
                ]),
                'catalog_dirs' => (new Type\VariableList())
                    ->setDescription(t('Товары из категории на главной'))
                    ->setTableFields([
                        new Type\VariableList\TextVariableListField('dir_title', t('Название категории')),
                        new Type\VariableList\SelectVariableListField('dir', t('Категория'), DirApi::selectList()),
                        new Type\VariableList\TextVariableListField('dir_products_num', t('Какое кол-во товаров отображать в категории?')),
                    ])
                    ->setRuntime(false),
                'toast_notifications' => new Type\ArrayList([
                    'description' => t('Всплывающие уведомления в приложении'),
                    'Attr' => [['size' => 5,'multiple' => 'multiple', 'class' => 'multiselect']],
                    'List' => [['\MobileSiteApp\Model\AppApi', 'getToastNotificationsFields']],
                    'CheckboxListView' => true,
                    'runtime' => false,
                ]),
                'show_news_block' => new Type\Integer([
                    'description' => t('Добавить блок "Новости" в личном кабинете'),
                    'hint' => t('Будут отображаться только отмеченные новости'),
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'enable_checkout_userfields' => new Type\Varchar([
                    'description' => t('Использовать дополнительные поля в оформлении заказа'),
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'enable_password_recovery' => new Type\Varchar([
                    'description' => t('Добавить возможность восстановления пароля'),
                    'hint' => t('При включении, в приложении появится возможность восстановления пароля пользователя'),
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'enable_collapse_expand_child_dirs' => new Type\Varchar([
                    'description' => t('Добавить возможность свернуть/развернуть дочерние категории в списке товаров'),
                    'hint' => t('При включении, у слайдера дочерних категорий появится кнопка свернуть/развернуть '),
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'enable_support' => new Type\Varchar([
                    'description' => t('Включить раздел поддержки'),
                    'hint' => t('При включении, у пользователя появится возможность обратиться в службу поддержки'),
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'minutes_to_send_push_new_message' => new Type\Integer([
                    'description' => t('Через сколько минут отправлять уведомление о новом сообщении в поддержке'),
                    'hint' => t('Если пользователь в данный момент просматривает тему, то PUSH не будет отправляться.<br>
                                        Если пользователь не просматривает тему, то PUSH будет отправляться через указанное время'),
                    'default' => 1,
                    'attr' => [[
                        'size' => 2,
                    ]],
                    'template' => '%mobilesiteapp%/form/admin/minutes_to_send_push_new_message.tpl',
                ]),
                'main_banner' => new Type\Integer([
                    'description' => t('Отображать главный баннер в приложении'),
                    'list' => [['\Banners\Model\ZoneApi','staticSelectList'], ['hide' => t('Не отображать')]],
                ]),
                'enable_bonus_card' => new Type\Varchar([
                    'description' => t('Отображать бонусную карту в приложении'),
                    'hint' => t('При включении, в приложении появится возможность отобразить и выпустить бонусную карту для пользователя'),
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'enable_last_comments' => new Type\Varchar([
                    'description' => t('Отображать последние отзывы к товарам на главной?'),
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'enable_brands' => new Type\Varchar([
                    'description' => t('Отображать бренды на главной?'),
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'enable_ask_affiliate' => new Type\Varchar([
                    'description' => t('Запрашивать филиал при запуске приложения?'),
                    'hint' => t('При первом запуске приложения будет запрашиваться филиал'),
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'bonus_card_hint' => new Type\Richtext([
                    'description' => t('Подсказка к бонусной карте'),
                    'hint' => t('Отображается на странице просмотра бонусной карты'),
                ]),
                'bonus_card_title' => new Type\Varchar([
                    'description' => t('Заголовок подробного просмотра бонусной карты'),
                ]),
                'onboarding_id' => new Type\Varchar([
                    'description' => t('ID текущей сессии OnBoarding'),
                    'hint' => t('Сгенерируйте новый ID сессии, если нужно <b>заново</b> показать OnBoarding в приложении'),
                    'runtime' => false,
                    'default' => 'onboarding_id_',
                    'template' => '%mobilesiteapp%/form/onboarding_id_generator.tpl'
                ]),
            t('Релиз'),
                'required_version_ios' => new Type\Varchar([
                    'description' => t('iOS - минимальная версия для запуска'),
                    'attr' => array(array(
                        'size' => 10
                    )),
                    'default' => '0.0.0'
                ]),
                'required_version_android' => new Type\Varchar([
                    'description' => t('Android - минимальная версия для запуска'),
                    'attr' => array(array(
                        'size' => 10
                    )),
                    'default' => '0.0.0'
                ]),
        ]);
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     * @throws ModuleException
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
                'tools' => [
                    [
                        'url' => RouterManager::obj()->getAdminUrl(false, [], 'mobilesiteapp-onboardingctrl'),
                        'title' => t('Настроить onboarding'),
                        'description' => t('Здесь вы сможете создать, изменить, удалить onboarding для приложения'),
                        'class' => ' '
                    ],
                ]
            ];
    }
}
