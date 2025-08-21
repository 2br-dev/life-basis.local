<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Config;

use Catalog\Model\ProductDimensions;
use Exchange\Model\Api;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\ConfigObject;
use RS\Orm\Type;

class File extends ConfigObject
{
    const ACTION_NOTHING      = "nothing";
    const ACTION_CLEAR_STOCKS = "clear_stocks";
    const ACTION_DEACTIVATE   = "deactivate";
    const ACTION_REMOVE       = "remove";

    const FULL_NAME_IGNORE               = -1;
    const FULL_NAME_TO_SHORT_DESCRIPTION = 0;
    const FULL_NAME_TO_DESCRIPTION       = 1;
    const FULL_NAME_TO_TITLE             = 2;

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'import_scheme' => new Type\Varchar([
                    'description' => t('Схема импорта'),
                    'listFromArray' => [[
                        'standard' => t('Стандарт'),
                        'offers_in_import' => t('2.07 - комплектации товара в файле import.xml'),
                    ]],
                    'template' => '%exchange%/form/config/import_scheme.tpl',
                ]),
                'import_offers_in_one_product' => new Type\Integer([
                    'description' => t('Импортировать комплектации как один товар'),
                    'checkboxView' => [1, 0],
                ]),
                'file_limit' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Размер единовременно загружаемой части файла (в байтах)'),
                ]),
                'use_zip' => new Type\Integer([
                    'description' => t('Использовать сжатие zip, если доступно'),
                    'checkboxview' => [1,0],
                ]),
                'history_depth' => new Type\Integer([
                    'description' => t('Сколько последних обменов хранить на сайте?'),
                    'hint' => t('Если установить "0" - файлы обмена не будут сохраняться в истории')
                ]),
                'dont_check_sale_init' => new Type\Integer([
                    'description' => t('Не проверять блокировку обмена при обработке файла заказов'),
                    'checkboxview' => [1,0],
                    'default' => 0,
                ]),
                'lock_expire_interval' => new Type\Integer([
                    'description' => t('Время жизни блокировки обмена, сек'),
                    'hint' => t('Должен с небольшим запасом покрывать время выполнения одного шага импорта')
                ]),
                'export_ip_in_official_name' => new Type\Integer([
                    'description' => t('Выгружать покупателя ИП как юр.лицо в 1С'),
                    'checkboxView' => [1,0],
                    'hint' => t('Установка данного флага будет означать, что выгрузка наименования ИП будет происходить в теге Контрагент->ОфициальноеНаименование, что будет принудительно устанавливать ВидКонтрагента в 1С как Юридическое лицо. Включайте только, если желаете активировать устаревшую логику передачи сведений в 1С, актуально для старых версий 1С.')
                ]),
            t('Каталог товаров - импорт'),
                'cat_for_import' => new Type\Integer([
                    'description' => t('Корневая категория импорта'),
                    'tree' => [['\Catalog\Model\DirApi', 'staticTreeList'], 0, [0 => t('- Корень каталога -')]],
                ]),
                'cat_for_catless_porducts' => new Type\Integer([
                    'description' => t('Категория для товаров без категории?'),
                    'tree' => [['\Catalog\Model\DirApi', 'staticTreeList'], 0, [0 => t('- Корень каталога -')]],
                ]),
                'catalog_import_interval' => new Type\Integer([
                    'maxLength' => '10',
                    'description' => t('Интервал одного шага в секундах (0 - выполнять загрузку за один шаг):'),
                ]),
                'catalog_element_action' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Что делать с товарами, отсутствующими в файле импорта'),
                    'listfromarray' => [[
                        self::ACTION_NOTHING      => t('Ничего'),
                        self::ACTION_CLEAR_STOCKS => t('Обнулять остаток'),
                        self::ACTION_DEACTIVATE   => t('Деактивировать'),
                        self::ACTION_REMOVE       => t('Удалить'),
                    ]],
                ]),
                'catalog_offer_action' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Что делать с комплектациями, отсутствующими в файле импорта'),
                    'listfromarray' => [[
                        self::ACTION_NOTHING => t('Ничего'),
                        self::ACTION_REMOVE  => t('Удалять'),
                    ]],
                ]),
                'catalog_section_action' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Что делать с категориями, отсутствующими в файле импорта'),
                    'listfromarray' => [[
                        self::ACTION_NOTHING    => t('Ничего'),
                        self::ACTION_DEACTIVATE => t('Деактивировать'),
                        self::ACTION_REMOVE     => t('Удалить'),
                    ]],
                ]),
                'product_uniq_field' => new Type\Varchar([
                    'description' => t('Что считать уникальным идентификатором товара?'),
                    'listFromArray' => [[
                        'xml_id' => t('Идентификатор 1С'),
                        'barcode' => t('Артикул'),
                        'title' => t('Наименование'),
                    ]],
                ]),
                'is_unic_dirname' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Идентифицировать категории по наименованию'),
                    'hint' => t('Используется для импорта уникальных идентификаторов'),
                    'checkboxview' => [1,0],
                ]),
                'hide_new_products' => new Type\Integer([
                    'description' => t('Скрывать новые товары'),
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                    'default' => 0,
                ]),
                'hide_new_dirs' => new Type\Integer([
                    'description' => t('Скрывать новые категории'),
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                    'default' => 0,
                ]),
                'import_short_description' => new Type\Integer([
                    'description' => t('Импортировать описание товара в поле "Краткое описание"'),
                    'checkboxView' => [1, 0],
                ]),
                'full_name_to_description' => new Type\Integer([
                    'description' => t('Как обрабатывать реквизит "Полное наименование"'),
                    'hint' => t('При обмене с сервисом МойСклад, нужно выбирать: Записывать в описание товара'),
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                    'default' => 0,
                    'listFromArray' => [[
                        self::FULL_NAME_IGNORE => t('Игнорировать'),
                        self::FULL_NAME_TO_SHORT_DESCRIPTION => t('Записывать в короткое описание товара'),
                        self::FULL_NAME_TO_DESCRIPTION => t('Записывать в описание товара'),
                        self::FULL_NAME_TO_TITLE => t('Записывать в название')
                    ]]
                ]),
                'offer_name_from_props' => new Type\Integer([
                    'description' => t('Формировать название комплектации из её характеристик'),
                    'hint' => t('Применяется если сторонняя система выгружает комплектации с одинаковыми названиями'),
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                    'default' => 0,
                ]),
                'dimensions_unit' => new Type\Enum(array_keys(ProductDimensions::handbookDimensionsUnits()), [
                    'description' => t('Единица измерения габаритов товаров в 1С, если они представлены в качестве реквизитов'),
                    'hint' => t('Система автоматически загрузит и конвертирует Длину, Ширину, Высоту (если они выгружены из 1С как реквизиты) и установит эти значения в характеристики товара (указанные в настройках модуля Каталог)'),
                    'listFromArray' => [ProductDimensions::handbookDimensionsUnits()],
                ]),

            t('Каталог товаров - что следует обновлять'),
                'catalog_keep_spec_dirs' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Сохранять связь товаров со спецкатегориями'),
                    'checkboxview' => [1,0],
                ]),
                'catalog_update_parent' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Обновлять зависимости категорий друг от друга'),
                    'checkboxview' => [1,0],
                ]),
                'product_update_dir' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Обновлять категории у товаров'),
                    'checkboxview' => [1,0],
                ]),
                'dont_delete_prop' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'description' => t('Не удалять характеристики товаров созданные на сайте'),
                    'checkboxview' => [1,0],
                ]),
                'dont_delete_costs' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Не удалять значения цен, созданные на сайте'),
                    'checkboxview' => [1,0],
                ]),
                'dont_delete_stocks' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Не удалять остатки на складах, созданные на сайте'),
                    'checkboxview' => [1,0],
                ]),
                'dont_update_fields' => new Type\ArrayList([
                    'description' => t('Поля товара, которые не следует обновлять'),
                    'Attr' => [['size' => 5,'multiple' => 'multiple', 'class' => 'multiselect']],
                    'List' => [['\Exchange\Model\Api', 'getUpdatableProductFields']],
                    'CheckboxListView' => true,
                    'runtime' => false,
                ]),
                'dont_update_offer_fields' => new Type\ArrayList([
                    'description' => t('Поля комплектаций, которые не следует обновлять'),
                    'Attr' => [['size' => 5,'multiple' => 'multiple', 'class' => 'multiselect']],
                    'List' => [['\Exchange\Model\Api', 'getUpdatableOfferFields']],
                    'CheckboxListView' => true,
                    'runtime' => false,
                ]),
                'dont_update_group_fields' => new Type\ArrayList([
                    'description' => t('Поля категории, которые не следует обновлять'),
                    'Attr' => [['size' => 5,'multiple' => 'multiple', 'class' => 'multiselect']],
                    'List' => [['\Exchange\Model\Api', 'getUpdatableGroupFields']],
                    'CheckboxListView' => true,
                    'runtime' => false,
                ]),
                'dont_update_prop_fields' => new Type\ArrayList([
                    'description' => t('Поля характеристики, которые не следует обновлять'),
                    'Attr' => [['size' => 5,'multiple' => 'multiple', 'class' => 'multiselect']],
                    'List' => [['\Exchange\Model\Api', 'getUpdatablePropFields']],
                    'CheckboxListView' => true,
                    'runtime' => false,
                ]),
            t('Каталог товаров - обработка данных'),
                'remove_offer_from_product_title' => new Type\Integer([
                    'description' => t('Удалять наименование комплектации из наименования товара'),
                    'hint' => t('Работает только для стандартного случая:<br>когда наименование комплектации записано в скобках в конце наименования товара'),
                    'checkboxView' => [1, 0],
                    'maxLength' => '1',
                ]),
                'catalog_translit_on_add' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Транслитерировать символьный код из названия при добавлении товара или каталога'),
                    'checkboxview' => [1,0],
                ]),
                'catalog_translit_on_update' => new Type\Integer([
                    'description' => t('Транслитерировать символьный код из названия при обновлении товара или каталога'),
                    'checkboxview' => [1,0],
                    'template' => '%exchange%/form/config/config_translit_checkbox.tpl'
                ]),
                'brand_property' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Характеристика отвечающая за производителя товара'),
                    'list' => [['\Catalog\Model\PropertyApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                    'hint' => t('Если бренд вашего товара указан в свойстве в 1С, то ReadyScript будет брать значение из характеристики и записывать его в поле товара Бренд'),
                ]),
                'import_brand' => new Type\Integer([
                    'description' => t('Импортировать бренд товара из поля "Изготовитель"'),
                    'checkboxview' => [1,0],
                    'default' => 0,
                ]),
                'weight_property' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Характеристика отвечающая за вес товара'),
                    'hint' => t('Если вес вашего товара указан в свойстве в 1С, то ReadyScript будет брать значение из характеристики и записывать его в поле товара Вес'),
                    'list' => [['\Catalog\Model\PropertyApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                ]),
                'multi_separator_fields' => new Type\Varchar([
                    'description' => t('Разделитель множественного значения в строке 1С'),
                    'maxLength' => 1,
                    'hint' => t('Включает обработку множественного свойства.<br/> 
                        При указании в свойстве 1С, типа строка, данного разделителя,<br/> 
                        оно будет воспринято как множемтвенное свойство'),
                    'Attr' => [['size' => '5']],
                    'default' => '', 
                    'runtime' => false
                ]),
                'allow_insert_multioffers' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'description' => t('Использовать импорт многомерных комплектаций'),
                    'hint' => t('Позволяет включить импорт многомерных комплектаций из 1С.<br/> При включении опции, включается опция<br/> "Не удалять характеристики созданные на сайте"'),
                    'checkboxview' => [1,0],
                ]),
                'unique_offer_barcode' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'description' => t('Уникализировать артикул комплектации при обмене?'),
                    'hint' => t('Добавляет уникальное окончание к артикулу переданному из 1С'),
                    'checkboxview' => [1,0],
                ]),
                'sort_offers_by_title' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'description' => t('Сортировать комплектации по наименованию'),
                    'hint' => t('Сортирует комплектации товаров методом NaturalSort (с учетом чисел в строках)'),
                    'checkboxview' => [1,0],
                ]),
                'force_delete_images' => new Type\Integer([
                    'maxLength' => 1,
                    'checkboxview' => [1, 0],
                    'description' => t('Удалять изображения товаров на сайте, ранее загруженные из 1С, если они отсутствуют в файле обмена'),
                    'hint' => t('Актуально использовать только если в 1C всегда включена опция -"выгружать файлы изображений"')
                ]),
            t('Заказы'),
                'sale_export_only_payed' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Выгружать только оплаченные заказы'),
                    'checkboxview' => [1,0],
                ]),
                'sale_export_statuses' => new Type\ArrayList([
                    'description' => t('Выгружать заказы со статусами'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList']],
                    'attr' => [[
                        AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                    ]],
                    'runtime' => false,
                ]),
                'sale_final_status_on_delivery' => new Type\Integer([
                    'description' => t('Статус, в который переводить заказ при получении флага "Проведён" от "1С:Предприятие"'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('-Не выбрано-')]],
                ]),
                'sale_final_status_on_pay' => new Type\Integer([
                    'description' => t('Статус, в который переводить заказ при получении оплаты от "1С:Предприятие"'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('-Не выбрано-')]],
                ]),
                'sale_final_status_on_shipment' => new Type\Integer([
                    'description' => t('Статус, в который переводить заказ при получении отгрузки от "1С:Предприятие"'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('-Не выбрано-')]],
                ]),
                'sale_final_status_on_success' => new Type\Integer([
                    'description' => t('Статус, в который переводить заказ при получении статуса "Исполнен" от "1С:Предприятие"'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('-Не выбрано-')]],
                ]),
                'sale_final_status_on_cancel' => new Type\Integer([
                    'description' => t('Статус, в который переводить заказ при получении флага "Отменён" от "1С:Предприятие"'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('-Не выбрано-')]],
                ]),
                'order_flag_cancel_requisite_name' => new Type\Varchar([
                    'description' => t('Название реквизита, содержащего флаг "Отменён"'),
                    'hint' => t('В некоторых редакциях 1С УТ 10.3 флаг "Отменён" имеет отличное наименование'),
                    'listFromArray' => [[
                        'Отменен' => t('Отменен'), 
                        'ПометкаУдаления' => t('ПометкаУдаления'),
                    ]],
                ]),
                'sale_replace_currency' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Заменять валюту при выгрузке в "1С:Предприятие" на'),
                ]),
                'order_update_status' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'description' => t('Обновлять статусы заказов при обмене'),
                    'checkboxview' => [1,0],
                ]),
                'export_timezone' => new Type\Varchar([
                    'description' => t('Выгружать заказы в часовом поясе'),
                    'default' => 'default',
                    'list' => [function() {
                        $list = \DateTimeZone::listIdentifiers();
                        $result = ['default' => t('По умолчанию')];
                        foreach($list as $item) {
                            $time = new \DateTime('now', new \DateTimeZone($item));
                            $result[$item] = $item." (UTC".$time->format('P').")";
                        }
                        return $result;
                    }]
                ]),
                'uniq_delivery_id' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Уникализировать id доставки при экспорте<br>(для сервиса МойСклад)'),
                    'checkboxview' => [1,0],
                ]),
                'one_step_export_limit' => new Type\Integer([
                    'description' => t('Какое количество заказов выгружать за один раз в 1С')
                ]),
                'import_on_sale_file' => new Type\Integer([
                    'description' => t('Импортировать заказы сразу после получения файла'),
                    'hint' => t('Установите данный флажок, если заказы из 1С отдаются на сайт без подтверждения. В случае установки флага импорт заказов будет происходить во время запроса type=sale&mode=file, иначе во время запроса type=sale&mode=import.'),
                    'checkboxView' => [1,0]
                ]),
                'export_free_delivery' => new Type\Integer([
                    'description' => t('Выгружать доставку, даже с нулевой стоимостью'),
                    'hint' => t('Без установки данной опции, в 1С доставка попадает только с положительной стоимостью'),
                    'checkboxView' => [1,0]
                ])
        ]);
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     * @throws \RS\Module\Exception
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'use_zip'       => Api::isZipAvailable() ? 1 : 0,
            'catalog_element_action'        => self::ACTION_NOTHING,
            'catalog_section_action'        => self::ACTION_NOTHING,
            'sale_export_statuses'  => [], // По умолчанию все статусы заказов
            /*
            'tools' => array(
                array(
                    'url' => RouterManager::obj()->getAdminUrl('ajaxFindBrands', array(), 'exchange-ctrl'),
                    'title' => t('Обновить бренды из характеристик'),
                    'description' => t('Собирает характеристики брендов и добавляет бренды')
                ),
            )*/
            ];
    }
    
    /**
     * Срабатывает перед записью конфига
     *
     * @param string $flag - insert или update
     * @return void
     */
    function beforeWrite($flag){
        //Добавим условия зависимости для импорта многомерных комплектаций 
        if ($this['allow_insert_multioffers'] && !$this['dont_delete_prop']){
            $this['dont_delete_prop'] = 1;
        }
    }
}
