<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Orm\OneClickItem;
use RS\Config\Loader as ConfigLoader;

/**
 * Класс содержит API функции дополтельные для работы в системе в рамках задач по модулю каталога
 */
class ApiUtils
{

    /**
     * Возвращает секцию с дополнительными полями купить в один клик из конфига для внешнего API
     *
     */
    public static function getAdditionalBuyOneClickFieldsSection()
    {
        //Добавим доп поля для покупки в один клик корзины
        $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
        $click_fields_manager->setErrorPrefix('clickfield_');
        $click_fields_manager->setArrayWrapper('clickfields');

        //Пройдёмся по полям
        $fields = [];
        foreach ($click_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;
            }
            $fields[] = $field;
        }

        return $fields;
    }

    /**
    * Добавляет секцию цены товарам, розничную и зачёркнутую
    * 
    * @param $list - список из объектов товаров
    */
    static function addProductCostValuesSection($list){
        $product = new \Catalog\Model\Orm\Product();    
        $product->getPropertyIterator()->append([
                'cost_values' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Розничная и зачёркнутая цена товара'),
                    'appVisible' => true
                ])
        ]);
        foreach($list as $product) {         
            $product_cost = [];
            $product_cost['cost']            = $product->getCost(null, null, false);
            $product_cost['cost_format']     = \RS\Helper\CustomView::cost($product_cost['cost'], \Catalog\Model\CurrencyApi::getCurrentCurrency()->stitle);
            $product_cost['old_cost']        = $product->getOldCost(null, false);
            $product_cost['old_cost_format'] = \RS\Helper\CustomView::cost($product_cost['old_cost'], \Catalog\Model\CurrencyApi::getCurrentCurrency()->stitle);
            $product['cost_values']          = $product_cost;            
        }
        
        return $list;
    }
    
    /**
    * Расширяет объекты характеристик для фильтров. Добавляет секции для видимости в экспорты.
    * 
    */
    private static function extendFiltersObjects()
    {
        $prop_group = new \Catalog\Model\Orm\Property\Dir();
        $prop_group->getPropertyIterator()->append([
            'properties' => new \RS\Orm\Type\ArrayList([
                'description' => t('Характеристики со значениями'),
                'appVisible' => true
            ])
        ]);
        $prop_item = new \Catalog\Model\Orm\Property\Item();
        $prop_item->getPropertyIterator()->append([
            'allowed_values' => new \RS\Orm\Type\ArrayList([
                'description' => t('Характеристики со значениями'),
                'appVisible' => true
            ]),
            'sortn' => new \RS\Orm\Type\Integer([
                'maxLength' => '11',
                'description' => t('Сорт. индекс'),
                'appVisible' => true
            ]),
            'group_id' => new \RS\Orm\Type\Integer([
                'maxLength' => '11',
                'runtime' => true,
                'appVisible' => true
            ]),
            'public' => new \RS\Orm\Type\Integer([
                'runtime' => true,
                'appVisible' => true
            ]),
            'interval_from' => new  \RS\Orm\Type\Real([
                'description' => t('Минимальное значение'),
                'Attr' => [['size' => 8]],
                'appVisible' => true,
                'runtime' => true
            ]),
            'interval_to' => new  \RS\Orm\Type\Real([
                'description' => t('Максимальное значение'),
                'Attr' => [['size' => 8]],
                'appVisible' => true,
                'runtime' => true
            ]),
        ]);
        $prop_item_value = new \Catalog\Model\Orm\Property\ItemValue();
        $prop_item_value->getPropertyIterator()->append([
            'color' => new \RS\Orm\Type\Color([
                'description' => t('Цвет'),
                'appVisible' => true,
            ])
        ]);
    }
    
    /**
    * Подготавливает секцию с картинками
    * 
    * @param mixed $image_orm - объект картинки
    * @return array
    */
    static function prepareImagesSection($image_orm)
    {
        if ($image_orm instanceof \RS\Orm\Type\Image) {
            $data = [
                'original_url' => $image_orm->getLink(true),
                'big_url' => $image_orm->getUrl(1000, 1000, 'xy', true),
                'middle_url' => $image_orm->getUrl(600, 600, 'xy', true),
                'small_url' => $image_orm->getUrl(300, 300, 'xy', true),
                'micro_url' => $image_orm->getUrl(100, 100, 'xy', true),
                'nano_url' => $image_orm->getUrl(50, 50, 'xy', true),
            ];
        } else {
            $data = [
                'id' => $image_orm['id'],
                'title' => $image_orm['title'],
                'original_url' => $image_orm->getOriginalUrl(true),
                'big_url' => $image_orm->getUrl(1000, 1000, 'xy', true),
                'middle_url' => $image_orm->getUrl(600, 600, 'xy', true),
                'small_url' => $image_orm->getUrl(300, 300, 'xy', true),
                'micro_url' => $image_orm->getUrl(100, 100, 'xy', true),
                'nano_url' => $image_orm->getUrl(50, 50, 'xy', true),
            ];
        }
        return $data;
    }
    
    /**
    * Подготавливает секцию для списковых значений в виде картинок характеристики.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyImageSection($prop){
        //Посмотрим на допустимые значения
        $values = [];
        $allowed_values = $prop->getAllowedValuesObjects();
        foreach ($allowed_values as $allowed_value){
            /**
            * @var \Catalog\Model\Orm\Property\ItemValue $allowed_value
            */
            $value          = \ExternalApi\Model\Utils::extractOrm($allowed_value);
            if ($allowed_value['image']){
                $value['image'] = self::prepareImagesSection($allowed_value->__image);
            }
            $values[]       = $value;
        }
        $prop['allowed_values'] = $values;
        return $prop;
    }
    
    /**
    * Подготавливает секцию для списковых значений в виде цвета характеристики.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    *
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyColorSection($prop){
        //Посмотрим на допустимые значения
        $values = [];
        $allowed_values = $prop->getAllowedValuesObjects();
        foreach ($allowed_values as $allowed_value){
            /**
            * @var \Catalog\Model\Orm\Property\ItemValue $allowed_value
            */
            $value          = \ExternalApi\Model\Utils::extractOrm($allowed_value);
            if ($allowed_value['image']){
                $value['image'] = self::prepareImagesSection($allowed_value->__image);    
            }
            $values[]       = $value;
        }
        $prop['allowed_values'] = $values;
        return $prop;
    }
    
    /**
    * Подготавливает секцию для списковых значений характеристики.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyListSection($prop){
        $prop['allowed_values'] = \ExternalApi\Model\Utils::extractOrmList($prop->getAllowedValuesObjects());
        return $prop;
    }
    
    /**
    * Подготавливает секцию для числовых значений характеристик.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyIntSection($prop){
        return $prop;
    }

    /**
     * Подготавливает секцию для radio значений характеристик.
     *
     * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
     * @return \Catalog\Model\Orm\Property\Item
     */
    private static function preparePropertyRadioSection($prop){
        return $prop;
    }
    
    /**
    * Подготавливает секцию для строковых значений характеристик.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyStringSection($prop){
        if (!empty($prop['allowed_values'])){
            $values = [];
            foreach($prop['allowed_values'] as $value){
                $values[] = $value;
            }
            $prop['allowed_values'] = $values;
        }
        return $prop;
    }

    private static function preparePropertyBoolSection($prop)
    {
        return $prop;
    }
    
    /**
    * Преобразует характеристики для фильтров таким образом, чтобы появлялись секции для экспорта значений
    * 
    * @param array $prop_list - массив характеристик фильтров для преобразования
    *
    * @return array
    */
    static function prepareFiltersPropertyListSections($prop_list)
    {
        //Значения характеристик для фильтра
        $filters_list = [];
        //Расширим нужные объект для видимости секций
        self::extendFiltersObjects();
        
        foreach($prop_list as $item){
            $properties = [];
            foreach ($item['properties'] as $prop){
                $method_name  = "prepareProperty".$prop['type']."Section"; //Вызовем метод для обработки значений
                $property     = \ExternalApi\Model\Utils::extractOrm(self::$method_name($prop));   
                if ($property['type']!='int'){
                    unset($property['interval_from']);
                    unset($property['interval_to']);
                }
                $properties[] = $property;
            }
            //Добавим характеристики в группу
            $item['group']['properties'] = $properties; //Добавим преобразованные характеристики
            $filters_list[] = \ExternalApi\Model\Utils::extractOrm($item['group']);
        }
        
        return $filters_list;
    }

    /**
     * Возвращает тип комплектаций товара. Всего 4 - ('none', 'offers', 'multioffers', 'offers + multioffers', 'virtual multioffers')
     *
     * @param \Catalog\Model\Orm\Product $product - объект товара
     *
     * @return string
     */
    protected static function getProductOfferType($product)
    {
        if ($product->isVirtualMultiOffersUse()){ //Если есть виртуальные многомерные комплектации
            return 'virtual multioffers';
        }
        if ($product->isMultiOffersUse()){
            $multioffers = $product['multioffers']['levels'];
            $first_multioffer = reset($multioffers);

            if (!empty($first_multioffer['values'])){
                if ($product->isOffersUse()){ //Если есть многомерные комплектации и комплектации
                    $multioffers = $product['multioffers']['levels'];
                    return 'offers + multioffers';
                }
                return 'multioffers';
            }
        }

        $product->fillOffers();
        return 'offers'; //Если нет комплектаций
    }

    /**
     * Возвращает массив из многомерных комплектаций товара
     *
     * @param \Catalog\Model\Orm\Product $product
     * @return array
     */
    protected static function getProductMultiOffers($product)
    {
        $product->fillMultiOffersPhotos();
        $multioffers = $product['multioffers']['levels'];

        if (!empty($multioffers)){
            /**
             * @var \Catalog\Model\Orm\MultiOfferLevel $multioffer_level
             */
            foreach($multioffers as $multioffer_level){
                $property = $multioffer_level->getPropertyItem();
                $multioffer_level->getPropertyIterator()->append([
                    'values' => new \RS\Orm\Type\Integer([
                        'description' => t('Значения уровня многомерных комплектаций'),
                        'visible' => true,
                        'Appvisible' => true,
                    ]),
                    'property_type' => new \RS\Orm\Type\Varchar([
                        'description' => t('Тип характеристики'),
                        'visible' => true,
                        'Appvisible' => true,
                    ]),
                ]);

                $multioffer_level['property_type'] = $property['type']; //Укажем тип характеристики
                $multioffer_level['title']  = empty($multioffer_level['title']) ?  $multioffer_level['prop_title'] : $multioffer_level['title'];

                if (!empty($multioffer_level['values'])){
                    /**
                     * @var \Catalog\Model\Orm\Property\ItemValue $value
                     */
                    foreach ($multioffer_level['values'] as $key=>$value){
                        switch($multioffer_level['property_type']){ //Укажем значения для типов характеристики цвет и изображение
                            case 'color':
                                $value->getPropertyIterator()->append([
                                    'color' => new \RS\Orm\Type\Varchar([
                                        'description' => t('Цвет'),
                                        'visible' => true,
                                        'Appvisible' => true,
                                    ]),
                                ]);
                                $value['images'] = [];
                                if ($value['image']){ //Если есть картинка в виде цвета
                                    self::fillValueImages($value, $value->__image);
                                }
                                break;
                            case 'image':
                                $value['images'] = [];
                                if ($value['image']){ //Если есть картинка
                                    self::fillValueImages($value, $value->__image);
                                }
                                break;
                        }
                        if ($multioffer_level['is_photo']){ //Если нужно значения отображать как фото
                            $value['images'] = [];
                            if ($multioffer_level['values_photos'] && isset($multioffer_level['values_photos'][$value['value']])){ //Если фото присутствуют отмеченные.
                                self::fillValueImages($value, $multioffer_level['values_photos'][$value['value']]);
                            }
                        }
                    }

                }

                $multioffer_level['values'] = \ExternalApi\Model\Utils::extractOrmList($multioffer_level['values']);
            }
        }
        return \ExternalApi\Model\Utils::extractOrmList($multioffers);
    }

    /**
     * Возвращает комплектации из виртуальных многомерных
     *
     * @param \Catalog\Model\Orm\Product $product - объект товара
     * @return array
     */
    protected static function getProductOffersFromVirtual($product)
    {
        $virtual_offers = [];
        $virtual_multioffers = $product['virtual_multioffers']['items'];
        foreach ($virtual_multioffers as $product_id=>$offer){
            $virtual_offers[] = [
                'values' => $offer['values'],
                'product_id' => $product_id
            ];
        }
        return $virtual_offers;
    }

    /**
     * Возвращает массив из комплектаций товара
     *
     * @param \Catalog\Model\Orm\Product $product - объект товара
     * @return array
     */
    protected static function getProductOffers($product, $current_currency, $sections)
    {
        $offer = new \Catalog\Model\Orm\Offer();
        //Разрешим показ полей для выгрузки
        $offer->getPropertyIterator()->append([
            'num' => new \RS\Orm\Type\Integer([
                'description' => t('Остаток на складе'),
                'visible' => true,
            ]),
            'sortn' => new \RS\Orm\Type\Integer([
                'description' => t('Сортировочный индекс'),
                'visible' => true,
            ]),
            'propsdata_arr' => new \RS\Orm\Type\ArrayList([
                'description' => t('Характеристики комплектации'),
                'visible' => true,
            ]),
            '_propsdata' => new \RS\Orm\Type\ArrayList([
                'description' => t('Характеристики комплектации'),
                'visible' => false,
            ]),
            'cost_values' => new \RS\Orm\Type\ArrayList([
                'description' => t('Розничная и зачёркнутая цена товара'),
                'appVisible' => true,
            ]),
            'all_cost_values' => new \RS\Orm\Type\ArrayList([
                'description' => t('Все цены'),
                'appVisible' => true,
            ]),
            'button_type' => new \RS\Orm\Type\Integer([
                'description' => t('Тип кнопки в зависимости от комплектации, наличия и т.д.'),
                'visible' => true,
            ]),
            'stock_sticks' => new \RS\Orm\Type\MixedType([
                'description' => t('Риски запонености на складах'),
                'visible' => true,
            ]),
        ]);
        //Пройдём по комплектациям
        $offers = [];
        if (isset($product['offers']['items']) && !empty($product['offers']['items'])){
            if (in_array('stock', $sections)) { //Если нужно добавить риски остатков
                $product->fillOffersStockStars(); //Заполним данные по рискам
            }
            $offers = $product['offers']['items'];
            /**
             * @var \Catalog\Model\Orm\Offer $offer
             */
            foreach ($offers as $sortn=>$offer){
                $offer->fillStockNum();
                $stock_num = [];
                if (in_array('stock', $sections)){ //Если нужно добавить риски остатков
                    if (!empty($offer['stock_num'])){
                        foreach ($offer['stock_num'] as $warehouse_id=>$num){
                            $warehouse_info['warehouse_id']  = $warehouse_id;
                            $warehouse_info['num'] = $num;
                            $stock_num[] = $warehouse_info;
                        }
                        $offer['stock_num'] = $stock_num;
                    }

                    if (!empty($offer['sticks'])) {
                        $sticks = [];
                        foreach ($offer['sticks'] as $warehouse_id => $num) {
                            $sticks_info['warehouse_id'] = $warehouse_id;
                            $sticks_info['count'] = $num;
                            $sticks[] = $sticks_info;
                        }

                        $offer['stock_sticks'] = $sticks;
                    }
                }

                $offer['button_type'] = $product->getButtonTypeByOffer($sortn);
                //Добавим секцию с ценами
                $cost_values = [
                    'cost' => $product->getCost(null, $offer['id'], false),
                    'old_cost' => $product->getOldCost($offer['id'], false)
                ];
                $cost_values['cost_format'] = \RS\Helper\CustomView::cost($cost_values['cost'], $current_currency['stitle']);
                $cost_values['old_cost_format'] = \RS\Helper\CustomView::cost($cost_values['old_cost'], $current_currency['stitle']);

                $offer['cost_values'] = $cost_values;

                //Если нужно дописывать все цены
                if (in_array('costs', $sections)) {
                    $all_cost_values = [];
                    $costs = \Catalog\Model\CostApi::staticSelectList();
                    foreach ($costs as $cost_id=>$cost_title){
                        $offer_cost = $product->getCost($cost_id, $offer['sortn'], false);
                        $all_cost_values[] = [
                            "id" => $cost_id,
                            "title" => $cost_title,
                            "cost" => $offer_cost,
                            "cost_format" => \RS\Helper\CustomView::cost($offer_cost, $current_currency['stitle']),
                        ];
                    }
                    $offer['all_cost_values'] = $all_cost_values;
                }
            }
        }

        return \ExternalApi\Model\Utils::extractOrmList($offers);
    }

    /**
     * Заполняет значение списка картинками
     *
     * @param \Catalog\Model\Orm\Property\ItemValue $value - объект значения
     * @param mixed $image - картинка для добавления
     */
    private static function fillValueImages($value, $image)
    {
        $value->getPropertyIterator()->append([
            'images' => new \RS\Orm\Type\ArrayList([
                'description' => t('Список с картинками'),
                'visible' => true,
                'Appvisible' => true,
            ])
        ]);

        $value['images'] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
    }


    /**
     * Возвращает сведения по комплектациям товара
     *
     * @param $product - Объект товара
     * @param $sections дополнительные секции
     *
     * @return array
     */
    static function getOffersInfo($product, $sections)
    {
        $response = [];
        $current_currency = \Catalog\Model\CurrencyApi::getCurrentCurrency();
        //Посмотрим какой тип комплектаций существует для данного товара

        $response['response']['type'] = self::getProductOfferType($product);

        $multioffers = [];
        $virtual_offers = [];
        //Загрузим комплектации или многогомерные комплектации
        switch($response['response']['type']){
            case 'virtual multioffers':
                $product->fillOffers();
                $multioffers    = self::getProductMultiOffers($product);
                $virtual_offers = self::getProductOffersFromVirtual($product);

                foreach ($multioffers as $key=>$level) { // временная заглушка для виртуальных многомерок
                    $multioffers[$key]['property_type'] = 'list';
                }

                break;
            case 'offers + multioffers':
            case 'multioffers':
                $multioffers = self::getProductMultiOffers($product);
                break;
        }
        $offers = self::getProductOffers($product, $current_currency, $sections);

        if (!$product->shouldReserve() && in_array('stock', $sections)){//Если нужна секция со сведениями о складах и склады есть
            $stick_info=$product->getWarehouseStickInfo();
            if (!empty($stick_info['warehouses'])){
                $warehouses = [];
                foreach ($stick_info['warehouses'] as $warehouse){
                    $warehouse_info = \ExternalApi\Model\Utils::extractOrm($warehouse);
                    if ($warehouse['image']){
                        $warehouse_info['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($warehouse->__image);
                    }
                    $warehouses[] = $warehouse_info;
                }
                $stick_info['warehouses'] = $warehouses;
                $response['response']['stick_info'] = $stick_info;
            }
        }

        if (!empty($offers)){
            $response['response']['offers'] = $offers;
        }
        if (!empty($multioffers)){
            $response['response']['multioffers'] = $multioffers;
        }
        if (!empty($virtual_offers)){
            $response['response']['virtual_offers'] = $virtual_offers;
        }

        return $response;
    }

    /**
     * Возвращает сформированную информацию по комплектациям к товару из offers_json
     *
     * @param $product - объект товара
     * @param $options - объект товара
     * @return array
     */
    static function fillOffersFromJSON($product, array $options = []): array
    {
        $response = [];

        $options += [
            'disableCheckOffers' => true,
            'showStockNum' => true,
            'showAllCosts' => true,
            'images' => [
                'big_url' => ['width' => 1000, 'height' => 1000, 'scale' => 'xy', 'absolute' => true],
                'middle_url' => ['width' => 600, 'height' => 600, 'scale' => 'xy', 'absolute' => true],
                'small_url' => ['width' => 300, 'height' => 300, 'scale' => 'xy', 'absolute' => true],
                'micro_url' => ['width' => 100, 'height' => 100, 'scale' => 'xy', 'absolute' => true],
                'nano_url' => ['width' => 50, 'height' => 50, 'scale' => 'xy', 'absolute' => true],
            ],
            'property_images' => [
                'big_url' => ['width' => 1000, 'height' => 1000, 'scale' => 'xy', 'absolute' => true],
                'middle_url' => ['width' => 600, 'height' => 600, 'scale' => 'xy', 'absolute' => true],
                'small_url' => ['width' => 300, 'height' => 300, 'scale' => 'xy', 'absolute' => true],
                'micro_url' => ['width' => 100, 'height' => 100, 'scale' => 'xy', 'absolute' => true],
                'nano_url' => ['width' => 50, 'height' => 50, 'scale' => 'xy', 'absolute' => true],
            ],
        ];

        $offers = $product->getOffersJSON($options, true);

        if ($offers && is_array($offers)) {
            $current_currency = \Catalog\Model\CurrencyApi::getCurrentCurrency();

            //Тип комплектации
            $response['response']['type'] = self::getProductOfferTypeFromJSON($offers);

            //Секция со сведениями о складах и склады есть
            if (!$product->shouldReserve()){
                $stick_info= WareHouseApi::getWarehouseStickInfo();
                if (!empty($stick_info['warehouses'])){
                    $warehouses = [];
                    foreach ($stick_info['warehouses'] as $warehouse){
                        $warehouse_info = \ExternalApi\Model\Utils::extractOrm($warehouse);
                        if ($warehouse['image']){
                            $warehouse_info['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($warehouse->__image);
                        }
                        $warehouses[] = $warehouse_info;
                    }
                    $stick_info['warehouses'] = $warehouses;
                    $response['response']['stick_info'] = $stick_info;
                }
            }

            //Секция с комплектациями
            if (isset($offers['offers'])) {
                $response['response']['offers'] = self::fillOffersDataFromJSON($offers, $product, $current_currency);
            }

            //Секция с многомерными комплектациями
            if (isset($offers['levels'])) {
                $response['response']['multioffers'] = self::fillMultiOffersDataFromJSON($offers, $product);

            }

            //Секция с виртуальными комплектациями
            if (isset($offers['virtual'])) {
                $response['response']['virtual_offers'] = self::fillVirtualOffersDataFromJSON($offers);
            }
        }

        return $response;
    }

    /**
     * Возвращает тип комплектации
     *
     * @param $offer
     * @return string
     */
    static function getProductOfferTypeFromJSON($offer)
    {
        if (isset($offer['levels']) && is_array($offer['levels'])) {
            $offer_type = 'offers + multioffers';
            $levels = $offer['levels'];
            foreach ($levels as $level) {
                if (isset($level['isVirtual']) && $level['isVirtual'] == 1) {
                    $offer_type = 'virtual multioffers';
                    break;
                }
            }
            return $offer_type;
        }
        return 'offers';
    }

    /**
     * Заполняет секцию с комплектациями
     *
     * @param $offers - массив комплектаций из JSON
     * @param $product - объект товара
     * @param $current_currency - объект текущей валюты
     * @return array
     */
    static function fillOffersDataFromJSON($offers, $product, $current_currency)
    {
        $response_offers = [];
        $unset_fields = ['stock_num', 'info', 'photos', 'sticks', 'availableOn', 'price', 'oldPrice'];

        foreach ($offers['offers'] as $offer) {
            $response_offer = $offer;
            foreach ($unset_fields as $unset_field) {
                unset($response_offer[$unset_field]);
            }

            //Комплектация основная
            $response_offer['is_main_offer'] = isset($offers['mainOfferId']) && $offers['mainOfferId'] == $offer['id'];

            //Добавим остатки по складам
            if (isset($offer['stock_num'])) {
                $offer_stock_num = [];
                foreach ($offer['stock_num'] as $id => $stock) {
                    $offer_stock_num[] = [
                        'warehouse_id' => $id,
                        'num' => $stock,
                    ];
                }
                $response_offer['stock_num'] = $offer_stock_num;
            }

            //Добавим связанные фото
            if (isset($offer['photos'])) {
                $response_offer['photos_arr'] = $offer['photos'];
            }

            //Добавим цену комплектации
            if (isset($offer['price'])) {
                $offer['price'] = str_replace(' ', '', $offer['price']);
                $cost_values = [
                    'cost' => $offer['price'],
                    'cost_format' => \RS\Helper\CustomView::cost($offer['price'], $current_currency['stitle']),
                ];
                if (isset($offer['oldPrice'])) {
                    $offer['oldPrice'] = str_replace(' ', '', $offer['oldPrice']);

                    $cost_values['old_cost'] = $offer['oldPrice'];
                    $cost_values['old_cost_format'] = \RS\Helper\CustomView::cost($offer['oldPrice'], $current_currency['stitle']);
                }
                $response_offer['cost_values'] = $cost_values;
            }

            //Добавим все цены
            $all_cost_values = [];
            $costs = \Catalog\Model\CostApi::getFullCostList();
            foreach ($costs as $cost){
                if (isset($offer['prices'][$cost->id])) {
                    $offer_cost = str_replace(' ', '', $offer['prices'][$cost->id]);
                    $all_cost_values[] = [
                        "id" => $cost->id,
                        "title" => $cost->title,
                        "cost" => $offer_cost,
                        "cost_format" => \RS\Helper\CustomView::cost($offer_cost, $current_currency['stitle']),
                    ];
                }
            }
            $response_offer['all_cost_values'] = $all_cost_values;

            //Добавим тип кнопки у комплектации
            $response_offer['button_type'] = self::getButtonTypeByOfferFromJSON($product, $offer);

            //Добавим наличие на определённом складе в виде рисок
            if (isset($offer['sticks'])) {
                $stock_sticks = [];
                foreach ($offer['sticks'] as $warehouse_id => $count) {
                    $stock_sticks[] = [
                        'warehouse_id' => $warehouse_id,
                        'count' => $count,
                    ];
                }
                $response_offer['stock_sticks'] = $stock_sticks;
            }

            //Добавим массив с названием и значением комплектации
            if (isset($offer['info'])) {
                $propsdata_arr = [];
                foreach ($offer['info'] as $value) {
                    $propsdata_arr[$value[0]] = $value[1];
                }
                $response_offer['propsdata_arr'] = $propsdata_arr;
            }

            $response_offers[] = $response_offer;
        }

        return $response_offers;
    }

    /**
     * Заполняет секцию с многомерными комплектациями
     *
     * @param $offers - массив комплектаций из JSON
     * @param $product - объект товара
     * @return array
     */
    static function fillMultiOffersDataFromJSON($offers, $product)
    {
        $response_multioffers = [];

        foreach ($offers['levels'] as $level) {
            $response_multioffer = [];
            $response_multioffer['product_id'] = $product['id'];
            $response_multioffer['prop_id'] = $level['id'];
            $response_multioffer['title'] = $level['title'];
            $response_multioffer['property_type'] = $level['isVirtual'] ? 'list' : $level['type'];

            if (isset($level['values'])) {
                $values = [];
                foreach ($level['values'] as $value) {
                    $response_value = [];
                    $response_value['id'] = $value['id'];
                    $response_value['value'] = $value['text'];
                    $response_value['images'] = $value['image'] ?? null;
                    $response_value['color'] = $value['color'] ?? null;
                    $values[] = $response_value;
                }
                $response_multioffer['values'] = $values;
            }
            $response_multioffers[] = $response_multioffer;
        }

        return $response_multioffers;
    }

    /**
     * Заполняет секцию с виртуальными комплектациями
     *
     * @param $offers - массив комплектаций из JSON
     * @return array
     */
    static function fillVirtualOffersDataFromJSON($offers)
    {
        $response_virtual_offers = [];
        foreach ($offers['virtual'] as $virtual_product_info) {
            $virtual_product = [];
            $virtual_product['product_id'] = $virtual_product_info['product_id'];
            if (isset($virtual_product_info['info'])) {
                $values = [];
                foreach ($virtual_product_info['info'] as $info) {
                    $values[$info[0]] = $info[1];
                }
                $virtual_product['values'] = $values;
            }
            $response_virtual_offers[] = $virtual_product;
        }
        return $response_virtual_offers;
    }

    /**
     * Возвращает тип кнопки для показа в зависимости от переданной комплектации. Купить, заказать, не показывать. (buy|reservation|none)
     *
     * @param $product - объект товара
     * @param $offer - комплектация
     * @return string
     */
    static function getButtonTypeByOfferFromJSON($product, $offer)
    {
        $shop_config = ConfigLoader::byModule('shop');
        $can_be_reserved = $product->canBeReserved();
        $forced_reservation = $product->isReservationForced();
        $sale_status = $offer['sale_status'];

        $is_available = !$shop_config['check_quantity'] || $shop_config['check_quantity'] && $offer['num'] > 0;

        $button_type = 'none';

        if ($forced_reservation) {
            $button_type = 'reservation';
        }else {
            if (($sale_status == 'no_cost' || $sale_status == 'show_cost' || $sale_status == 'on_request') && $can_be_reserved && !$is_available) {
                $button_type = 'reservation';
            }
            if ($sale_status == 'show_cost' && $is_available) {
                $button_type = 'buy';
            }
            if ($sale_status == 'no_cost' && !$can_be_reserved && !$is_available) {
                $button_type = 'none';
            }
            if ($sale_status == 'on_request' ||  $sale_status == 'no_cost' && $is_available && $can_be_reserved) {
                $button_type = 'reservation';
            }
        }

        if ($sale_status == 'discontinued') {
            $button_type  = 'none';
        }


        return $button_type;
    }

    /**
     * Возвращает массив статусов для покупок в 1 клик
     *
     * @return array[]
     */
    static function getOneClickStatuses(): array
    {
        $status_titles = OneClickItem::getStatusTitles();
        return [
            [
                'title' => $status_titles[OneClickItem::STATUS_NEW],
                'id' => OneClickItem::STATUS_NEW,
                'color' => OneClickItem::STATUS_COLOR_NEW,
            ],
            [
                'title' => $status_titles[OneClickItem::STATUS_VIEWED],
                'id' => OneClickItem::STATUS_VIEWED,
                'color' => OneClickItem::STATUS_COLOR_VIEWED,
            ],
            [
                'title' => $status_titles[OneClickItem::STATUS_CANCELLED],
                'id' => OneClickItem::STATUS_CANCELLED,
                'color' => OneClickItem::STATUS_COLOR_CANCELLED,
            ],
        ];
    }

}