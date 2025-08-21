<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use Catalog\Model\CostApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\UnitApi;
use ExternalApi\Model\Orm\AuthorizationToken;
use ExternalApi\Model\Utils;
use Partnership\Model\Orm\Partner;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Config\UserFieldsManager;
use RS\Helper\CustomView;
use RS\Orm\Type;
use RS\Orm\Type\ArrayList;
use Shop\Config\ModuleRights;
use Shop\Model\DeliveryType\InterfaceDeliveryOrder;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\CartItem;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Reservation;
use Shop\Model\Orm\UserStatus;
use Users\Model\Orm\User;

/**
* Класс содержит методы, необходимые для внешнего API модуля Магазин
*/
class ApiUtils
{
    protected
        $cart; //Объект корзины
        
    /**
    * Возвращает секцию с дополнительными полями купить в один клик из конфига для внешнего API
     *
    * @return array
    */
    public static function getAdditionalBuyOneClickFieldsSection()
    {
        //Добавим доп поля для покупки в один клик корзины
        $click_fields_manager = Loader::byModule('catalog')->getClickFieldsManager();
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
    * Возвращает секцию с дополнительными полями заказа из конфига для внешнего API
     *
    * @return array
    */
    public static function getAdditionalOrderFieldsSection()
    {
        $order = new Order();
        $order_fields_manager = $order->getFieldsManager();
        
        //Пройдёмся по полям
        $fields = [];
        foreach ($order_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;    
            }
            $fields[] = $field;
        }
        
        return $fields;
    }
    
    /**
    * Подготавливает секции с комплектациями и многомерными комплектациями
    * 
    * @param array $item - массив данных одной записи в корзине
    * @param Product $product - объект товара
    * @param CartItem $cartitem - объект объекта корзины
    * @return array
    */
    private static function prepareOffersAndMultiOffersSection($item, $product, $cartitem)
    {
        $item['multioffers'] = null;
        $item['multioffers_string'] = ""; //В виде строки
        if ($product->isMultiOffersUse()){ //Если есть многомерные комплектации
            $item['type'] = 'multioffers';
            $multioffers_values = @unserialize((string)$cartitem['multioffers']);
            
            foreach ($product['multioffers']['levels'] as $level){
                $multioffer['title']   = !empty($level['title']) ? $level['title'] : $level['prop_title'];
                $multioffer['prop_id'] = $level['prop_id'];
                foreach ($level['values'] as $value){
                    if ($value['val_str'] == $multioffers_values[$level['prop_id']]['value']){
                       $multioffer['value'] = $value['val_str']; 
                    }
                }
                $item['multioffers'][] = $multioffer;
            }
            if ($product->isOffersUse()){ //Если комплектации тоже присутствуют
                foreach ($product['offers']['items'] as $key=>$offer){
                    if ($cartitem['offer'] == $key){
                        $item['model'] = $offer['title'];
                        $item['propsdata_arr'] = $offer['propsdata_arr'];
                    }
                }
            }
            // Многомерные комплектации строкой
            if (!empty($item['multioffers'])){
                $multioffers_string = [];
                foreach ($item['multioffers'] as $multioffer){
                   $m_value = $multioffer['value'] ?? "";
                   $m_title = $multioffer['title'] ?? "";
                   $multioffers_string[] = $m_title.": ".$m_value;
                }
                
                $item['multioffers_string'] = implode(", ", $multioffers_string);
            }
        } elseif ($product->isOffersUse()){ //Если есть только комплектации
            $item['offer_caption'] = $product['offer_caption'] ?: 'Комплектация';
            $item['type'] = 'offers';
            foreach ($product['offers']['items'] as $key => $offer) {
                if ($cartitem['offer'] == $key) {
                    $item['model'] = $offer['title'];
                }
            }
        }
        return $item;
    }
    
    
    /**
    * Подготавливает секцию с сопутствующими товарами
    * 
    * @param array $item - массив данных одной записи в корзине
    * @param Product $product - объект товара
    * @param CartItem $cartitem - объект объекта корзины
    */
    private static function prepareSubProducts($item, $product, $cartitem)
    {
        if (!empty($item['sub_products'])){
            $shop_config    = Loader::byModule('shop');
            $concomitant = $product->getConcomitant();    
            
            $sub_product_data_arr = [];
            //Переберём сопутствующие товары и добавим данные по ним
            foreach ($item['sub_products'] as $id => &$sub_product_data){
                /**
                * @var Product $sub_product
                */
                $sub_product = $concomitant[$id]; //Сопутствующий товар
                
                $sub_product_data['title']      = $sub_product['title'];
                $sub_product_data['product_id'] = $item['id'];
                $sub_product_data['image']      = \Catalog\Model\ApiUtils::prepareImagesSection($sub_product->getMainImage());
                $sub_product_data['id']         = $sub_product['id'];
                $sub_product_data['unit']       = $sub_product->getUnit()->stitle;
                
                
                $sub_product_data['allow_concomitant_count_edit'] = false;
                //Если позволено редактировать количество сопутствующих
                if ($shop_config['allow_concomitant_count_edit']){
                   $sub_product_data['allow_concomitant_count_edit'] = true; 
                }
                
                $sub_product_data_arr[] = $sub_product_data; 
            }
            $item['sub_products'] = $sub_product_data_arr;
        }
        return $item['sub_products'];
    }
    
    /**
    * Подготовка сведений о купонах, добавленных в корзине
    * 
    * @param array $cartdata - массив данных по составу корзины
    */
    private static function prepareCouponsInfo($cart, $cartdata){
        
        $coupons = $cart->getCouponItems();
        $cartdata['coupons'] = [];
        if (!empty($coupons)){
            foreach ($coupons as $id=>$item){
                $coupon['id']   = $id;
                $coupon['code'] = $item['coupon']['code'];
                $cartdata['coupons'][] = $coupon;
            }
        }    
        
        return $cartdata;
    }
    
    /**
    * Заполняет подробные данные по товарам в сведения корзины. 
    * Если объект заказа передан, то будут премешаны элеметы заказа  
    * 
    * @param Order $order - объект заказа
    * @return array
    */
    public static function fillProductItemsData($order = null)
    {
        $cart = $order ? $order->getCart() : Cart::currentCart();
        $cartdata = $cart->getCartData();
           
        if (!empty($cartdata['items'])){
            $catalog_config = Loader::byModule('catalog');
            
            $items = [];
            $product_items = $cart->getProductItems();
            $m = 0;
            //Сведения по товарам
            foreach($cartdata['items'] as $uniq => $item){
                /**
                * @var Product $product
                * @var CartItem $cartitem
                */
                $product   = $product_items[$uniq]['product'];
                $cartitem  = $product_items[$uniq]['cartitem'];
                
                //Дополним сведениями по самому товару
                $item['title']        = $product['title'];
                $item['image']        = \Catalog\Model\ApiUtils::prepareImagesSection($product->getOfferMainImage($cartitem['offer']));
                $item['entity_id']    = $cartitem['entity_id'];
                $item['amount']       = $cartitem['amount'];
                $item['amount_error'] = isset($item['amount_error']) ? $item['amount_error'] : "";
                $item['amount_step']  = $product->getAmountStep($cartitem['offer']);
                $item['offer']        = $cartitem['offer'];
                $item['model']        = null;
                $item['type']        = 'single';

                $item = self::prepareOffersAndMultiOffersSection($item, $product, $cartitem);
                
                if ($catalog_config['use_offer_unit']){ //Если нужно использовать единицы измерения в комплектациях
                    $product->fillOffers();
                    $item['unit'] = $product['offers']['items'][$cartitem['offer']]->getUnit()->stitle;
                }else{
                    $item['unit'] = $product->getUnit()->stitle;
                }
                
                $item['sub_products'] = self::prepareSubProducts($item, $product, $cartitem);

                $items[$m] = $item;
                $m++;
            }
            $cartdata['items'] = $items;
            
            //Сведения по купонам
              
            $cartdata = self::prepareCouponsInfo($cart, $cartdata);
            
            $taxes = [];
            if (!empty($cartdata['taxes'])) {
                foreach ($cartdata['taxes'] as $taxitem) {
                    $taxes[] = [
                        'title' => $taxitem['title'],
                        'cost' => $taxitem['cost']
                    ];
                }
            }
              
            $cartdata['taxes'] = $taxes;
        }
                                   
        if ($order){  //Если передан заказ       
            $cartdata['user']      = Utils::extractOrm($order->getUser());
            $cartdata['only_pickup_points'] = $order['only_pickup_points'];
            $cartdata['use_addr']  = $order['use_addr'];
            if ($order['delivery']){ //Обработаем доставку
                $order_delivery         = $cartdata['delivery'];
                $order_delivery         = Utils::extractOrm($order_delivery['object']);
                $order_delivery['cost'] = $cartdata['delivery']['cost'];
                $cartdata['delivery']   = $order_delivery;
                
            }
            if (isset($cartdata['payment_commission']) && $cartdata['payment_commission']){  //Обработаем коммисию за заказ
                $payment_commission             = $cartdata['payment_commission'];
                $payment_commission             = Utils::extractOrm($payment_commission['object']);
                $payment_commission['cost']     = $cartdata['payment_commission']['cost'];;
                $cartdata['payment_commission'] = $payment_commission;
            }
            $cartdata['payment']   = Utils::extractOrm($order->getPayment());
            $cartdata['warehouse'] = Utils::extractOrm($order->getWarehouse());
            $cartdata['address']   = Utils::extractOrm($order->getAddress());
        }
        
        return $cartdata;
    }
    
    
    /**
    * Возвращает список доставок по текущему оформляемому заказу из сессии
    * 
    * @param AuthorizationToken|false $token - токен приложения
    * @param Order $order - заказ для которого нужно вернуть доставки
    * @param string $sortn - сортировка элементов
    */
    public static function getOrderDeliveryListSection($token, $order, $sortn)
    {
        $errors        = [];
        $delivery_list = [];
        $warehouses    = [];
        $shop_config = Loader::byModule('shop'); //Конфиг магазина
              
        if (!$shop_config['hide_delivery']){

            $user = ($token) ? $token->getUser() : Auth::getCurrentUser();
            
            //Расширим объект, для подачи нужных полей
            $delivery = new Delivery();
            $delivery->getPropertyIterator()->append([
                'extrachange_discount_type' => new Type\Integer([
                    'visible' => true
                ]),
                'extra_text' => new Type\Varchar([
                    'visible' => true
                ]),
                'cost' => new Type\Varchar([
                    'visible' => true
                ]),
                'additional_html' => new Type\Varchar([
                    'visible' => true
                ]),
                'mobilesiteapp' => new Type\Integer([
                    'visible' => true,
                    'appVisible' => true
                ]),
                'error' => new Type\Varchar([
                    'visible' => true
                ]),
                'additional_fields' => new ArrayList([
                    'visible' => true
                ]),
                'clientsiteapp_additional_html' => new Type\Varchar([
                    'visible' => true
                ]),
            ]);

            $delivery_api = new DeliveryApi();
            $delivery_api->setFilter('mobilesiteapp_hide', 0);
            $delivery_list = $delivery_api->getCheckoutDeliveryList($user, $order);
            
            if (!empty($delivery_list)){
                foreach ($delivery_list as &$delivery){
                    /**
                    * @var Delivery $delivery
                    */
                    $delivery['error']           = $delivery->getTypeObject()->somethingWrong($order);     
                    $delivery['additional_fields'] = $delivery->getTypeObject()->getNonCityRequiredAddressFieldsObjects();
                    $delivery['extra_text']      = !$delivery['error'] ? $order->getDeliveryExtraText($delivery) : null;
                    $delivery['cost']            = !$delivery['error'] ? $order->getDeliveryCostText($delivery) : null;
                    $delivery['additional_html'] = $delivery->getAddittionalHtml($order);
                    $delivery['mobilesiteapp']   = 0; //Флаг, что предназначено для мобильного приложения в виде сайта
                    $delivery['mobilesiteapp_additional_html'] = "";
                    if (in_array('Shop\Model\DeliveryType\InterfaceIonicMobile', class_implements($delivery->getTypeObject()))){ //Добавим HTML для мобильной версии
                       $delivery['mobilesiteapp'] = 1;
                       $delivery['mobilesiteapp_additional_html'] = $delivery->getTypeObject()->getIonicMobileAdditionalHTML($order, $delivery);
                    }
                    $delivery['clientsiteapp_additional_html'] = $delivery->getTypeObject()->getClientSiteAppAdditionalHTML($order, $delivery);
                }
                $delivery_list = Utils::extractOrmList($delivery_list);
            }                                          
            
            $warehouses = \Catalog\Model\WareHouseApi::getPickupWarehousesPoints();
            if (!empty($warehouses)){
                $warehouses = Utils::extractOrmList($warehouses);
            }    
        }      
        
        $response['errors']     = $order->getErrors();
        $response['list']       = $delivery_list; 
        $response['warehouses'] = $warehouses; 
        return $response;
    }
    
    /**
    * Возвращает список оплат по текущему оформляемому заказу из сессии
    * 
    * @param string token - токен приложения
    * @param Order order - заказ для которого нужно вернуть доставки
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    public static function getOrderPaymentListSection($token, $order, $sortn)
    {
        $errors   = [];
        $pay_list = [];
        
        $shop_config = Loader::byModule('shop'); //Конфиг магазина
              
        if (!$shop_config['hide_payment']){
            $pay_item = new Payment();
            //Расширим объект, для подачи нужных полей
            $pay_item->getPropertyIterator()->append([
                'clientsiteapp_additional_html' => new Type\Varchar([
                    'visible' => true
                ]),
            ]);

            $user    = ($token) ? $token->getUser() : Auth::getCurrentUser();

            $pay_api = new PaymentApi();
            $pay_api->setFilter('mobilesiteapp_hide', 0);
            $pay_api->setOrder($sortn);
            $pay_list = $pay_api->getCheckoutPaymentList($user, $order);
            
            $delivery_id = $order['delivery'];   
            foreach ($pay_list as $k=>&$pay_item) {  //Переберём оплаты, чтобы ограничить под выбранную доставку
                $pay_item['clientsiteapp_additional_html'] = $pay_item->getTypeObject()->getClientSiteAppAdditionalHTML($order, $pay_item);;
               if (is_array($pay_item['delivery']) && !empty($pay_item['delivery']) && !in_array(0, $pay_item['delivery'])) { //Если есть прявязанные доставки
                  if (!in_array($delivery_id, $pay_item['delivery'])) {
                      unset($pay_list[$k]); 
                  }   
               }                 
            }
            
            $pay_list = Utils::extractOrmList($pay_list);
            
            //Найдём оплату по умолчанию, если оплата не была задана раннее
            if (!$order['payment']){
                $pay_api->setFilter('default_payment', 1);
                $default_payment = $pay_api->getFirst($order);
                if ($default_payment){
                    foreach ($pay_list as $k => $pay_item) {
                        $pay_list[$k]['default'] = 0;  
                        if ($pay_item['id'] == $default_payment['id']){
                            $pay_list[$k]['default'] = 1;    
                        }
                    }
                } 
            }
            
        }
        
        $response['errors'] = $errors;
        $response['list']   = $pay_list;
        return $response;
    }

    /**
     * Добавляет необходимые Runtime поля к OrderItem
     *
     * @return void
     */
    public static function appendRuntimeOrderItemProperties()
    {
        $prototype = new OrderItem();
        $prototype->getPropertyIterator()->append([
            'image' => new Type\MixedType([
                'description' => t('Фото'),
                'appVisible' => true
            ]),
            'url' => new Type\Varchar([
                'description' => t('Фото'),
                'appVisible' => true
            ]),
            'cost' => new Type\MixedType([
                'appVisible' => true
            ]),
            'cost_formatted' => new Type\MixedType([
                'appVisible' => true
            ]),
            'price_formatted' => new Type\MixedType([
                'appVisible' => true
            ]),
            'discount_formatted' => new Type\MixedType([
                'appVisible' => true
            ]),
            'unit_title' => new Type\MixedType([
                'appVisible' => true
            ]),
            'multioffers_values' => new Type\MixedType([
                'appVisible' => true
            ])
        ]);
    }

    /**
     * Извлекает данные из OrderItem массив данных, который будет возвращен в ответ на API запросы.
     * Предварительно обязательно вызвать appendRuntimeOrderItemProperties
     *
     * @param OrderItem $order_item
     * @return array
     */
    public static function extractOrderItem($order_item)
    {
        $currency_liter = CurrencyApi::getBaseCurrency()->stitle;
        $product = new Product($order_item['entity_id']);
        $product->fillOffers();
        $product->fillMultiOffers();

        $order_item['cost'] = $order_item['price'] - $order_item['discount'];
        $order_item['cost_formatted'] = CustomView::cost($order_item['cost'])." ".$currency_liter;
        $order_item['price_formatted'] = CustomView::cost($order_item['price'])." ".$currency_liter;
        $order_item['discount_formatted'] = CustomView::cost($order_item['discount'])." ".$currency_liter;
        $order_item['unit_title'] = $order_item->getUnit()->stitle;

        //Подгрузим картинки товаров
        if ($order_item['type'] == OrderItem::TYPE_PRODUCT) {
            if ($product['id']) {
                $order_item['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($product->getOfferMainImage($order_item['offer']));
            }
            $order_item['url'] = $product->getUrl(true);
        }

        $one_item = Utils::extractOrm($order_item);
        $one_item['offer_type'] = 'single';

        $multioffers_arr = [];
        $multioffers_values = [];
        $multioffers = @unserialize((string)$one_item['multioffers']);
        if (!empty($multioffers)){
            foreach ($multioffers as $prop_id => $multioffer){
                $multioffers_arr[] = $multioffer;
                $multioffers_values[$prop_id] = $multioffer['value'] ?? '';
            }
        }
        $one_item['multioffers'] = $multioffers_arr;
        $one_item['multioffers_values'] = $multioffers_values;

        if ($product->isMultiOffersUse() && $product->isOffersUse()) {
            $one_item['offer_type'] = 'multioffers';

            if ($product->isOffersUse()){ //Если комплектации тоже присутствуют
                foreach ($product['offers']['items'] as $key=>$offer){
                    if ($order_item['offer'] == $key){
                        $one_item['model'] = $offer['title'];
                        $one_item['propsdata_arr'] = $offer['propsdata_arr'];
                    }
                }
            }
        }elseif ($product->isOffersUse()){
            $one_item['offer_caption'] = $product['offer_caption'] ?: 'Комплектация';
            $one_item['offer_type'] = 'offers';
            foreach ($product['offers']['items'] as $key=>$offer){
                if ($one_item['offer'] == $key){
                    $one_item['model'] = $offer['title'];
                }
            }
        }

        return $one_item;
    }

    /**
     * Добавляет к заказу секцию с товарами
     *
     * @param Order $order
     * @return Order
     */
    public static function addOrderItems(Order $order)
    {
        $order->getPropertyIterator()->append([
            'dateof_iso' => new Type\MixedType([
                'description' => t('Дата создания в формате ISO 8601'),
                'appVisible' => true
            ]),
            'dateof_timestamp' => new Type\Integer([
                'description' => t('Дата создания в формате TIMESTAMP'),
                'appVisible' => true
            ]),
            'dateof_date' => new Type\Varchar([
                'description' => t('Дата создания в формате dd.mm.YYYYY'),
                'appVisible' => true
            ]),
            'total_discount_unformatted' => new Type\Decimal([
                'description' => t('Скидка на заказ без форматирования'),
                'appVisible' => true
            ]),
            'total_discount' => new Type\Varchar([
                'description' => t('Скидка на заказ'),
                'appVisible' => true
            ]),
            'dateof_datetime' => new Type\Varchar([
                'description' => t('Дата создания в формате dd.mm.YYYYY HH:ii'),
                'appVisible' => true
            ]),
            'items' => new Type\MixedType([
                'description' => t('Состав заказа'),
                'appVisible' => true
            ]),
            'track_url' => new Type\Varchar([
                'description' => t('Url для отслеживания'),
                'appVisible' => true
            ]),
            'files' => new Type\MixedType([
                'description' => t('Файлы прикреплённые к заказу'),
                'appVisible' => true
            ]),
            'additional_fields' => new Type\MixedType([
                'description' => t('Дополнительные поля заказа из модуля Магазин'),
                'appVisible' => true
            ]),
        ]);

        $items = [];

        self::appendRuntimeOrderItemProperties();

        $cart = $order->getCart();
        $cart_items = $cart->getItems();
        $cart_data = $cart->getCartData(false, false);
        foreach($cart_items as $order_item) {
            $items[] = self::extractOrderItem($order_item);
        }

        $timestamp_date = strtotime((string)$order['dateof']);
        if ($url=$order->getTrackUrl()){
            $order['track_url'] = $url;
        }

        //Если есть файлы привязанные к заказу
        if ($files = $order->getFiles()) {
            $order_files = [];
            foreach ($files as $file) {
                $order_files[] = [
                    'title' => $file['name'],
                    'link'  => $file->getHashedUrl(true)
                ];
            }
            $order['files'] = $order_files;
        }

        //Если есть дополнительные поля прописанные в модуле магазин
        $fm = $order->getFieldsManager();

        if ($additional_fields = $fm->getStructure()){
            $order_fields = [];
            foreach ($additional_fields as $order_field){
                $order_field_item = [
                    'title' => $order_field['title'],
                    'value'  => $order_field['current_val'],
                    'alias' => $order_field['alias'],
                    'type' => $order_field['type'],
                ];

                if ($order_field['type'] == UserFieldsManager::TYPE_LIST) {
                    $order_field_item['values'] = $fm->parseValueList($order_field['values']);
                }

                $order_fields[] = $order_field_item;
            }
            $order['additional_fields'] = $order_fields;
        }

        if ($cart_data['total_discount_unformatted'] && $cart_data['total_discount_unformatted'] > 0) {
            $order['total_discount_unformatted'] = $cart_data['total_discount_unformatted'];
            $order['total_discount'] = $cart_data['total_discount'];
        }

        $order['dateof_iso']       = date('c', $timestamp_date);
        $order['dateof_date']      = date('d.m.Y', $timestamp_date);
        $order['dateof_datetime']  = date('d.m.Y H:i', $timestamp_date);
        $order['dateof_timestamp'] = strtotime((string)$order['dateof']);
        $order['items']            = $items;

        return $order;
    }

    /**
     * Возвращает полную развернутую информацию о заказе в виде готового массива для отдачи,
     * включая все объекты, из которых состоит заказ.
     *
     * @param Order $object
     * @param bool $detail Если true, то будет возвращено больше информации. (для страницы детального просмотра заказа)
     * @return array
     */
    public static function getFullOrderResponse(Order $object, $detail = true)
    {
        $result = [
            'order' => Utils::extractOrm($object)
        ];

        $units = [];

        if (!empty($result['order']['items'])){
            foreach ($result['order']['items'] as &$cartitem){ //Добавим форматированную
                $cartitem['price_formatted'] = CustomView::cost($cartitem['price'])." ".$object['currency_stitle'];

                if ($cartitem['unit_id']) {
                    $units[$cartitem['unit_id']] = true;
                }
            }
        }

        if  ($result['order']['totalcost']) {
            $result['order']['totalcost_formatted'] = CustomView::cost($result['order']['totalcost'])." ".$object['currency_stitle'];
        }

        //Загружаем единицы измерения
        if ($units) {
            $default_unit = (int)Loader::byModule(__CLASS__)->default_unit;

            $unit_api = new UnitApi();
            $unit_api->setFilter('id', array_merge(array_keys($units), [$default_unit]), 'in');
            $unit_objects = Utils::extractOrmList($unit_api->getList(), 'id');

            //Загружаем единицу измерения по умолчанию.
            if ($default_unit && isset($units[0])) {
                $unit_objects[0] = $unit_objects[$default_unit];
            }
            $result['unit'] = $unit_objects;
        }

        //Можно ли оплатить заказ
        $result['order']['can_online_pay']  = $object->canOnlinePay();

        //Возможно ли редактирование заказа
        $result['order']['can_edit'] = $object->canEdit();

        $platforms = OrderApi::getCreatorPlatformsListTitles();
        $result['order']['creator_platform_title'] = $platforms[$result['order']['creator_platform_id']] ?? t('Неизвестно');

        if ($object->canOnlinePay()) {
            $result['order']['pay_url'] = $object->getOnlinePayUrl(true);
        }

        $result['order']['extra_info'] = $object->getExtraInfo();
        $result['order']['delivery_extra'] = $object->getExtraKeyPair(Order::EXTRAKEYPAIR_DELIVERY_EXTRA);

        if ($user_id = $object['user_id']) {
            $user = new User($user_id);
            $result['user'][$user_id] = Utils::extractOrm($user);
            $result['user'][$user_id]['priority_cost_id'] = $user->getUserTypeCostId() ?: CostApi::getDefaultCostId();
        }

        if ($object['courier_id'] && !isset($result['user'][$object['courier_id']])) {
            $result['user'][$object['courier_id']] =
                Utils::extractOrm(new User($object['courier_id']));
        }

        if ($object['manager_user_id'] && !isset($result['user'][$object['manager_user_id']])) {
            $result['user'][$object['manager_user_id']] =
                Utils::extractOrm(new User($object['manager_user_id']));
        }

        if ($status_id = $object['status']) {
            $status = new UserStatus($status_id);
            $result['status'][$status_id] = Utils::extractOrm($status);

            if ($detail && $status['type'] == UserStatus::STATUS_PAYMENT_METHOD_SELECTED) {
                $saved_payment_method = $object->getSavedPaymentMethod();
                if ($saved_payment_method) {
                    $result['order']['can_execute_payment_action'] = true;
                    $result['order']['saved_payment_method_title'] =
                        implode(' ', [
                            $saved_payment_method->getType(),
                            $saved_payment_method['subtype'],
                            $saved_payment_method['title']
                        ]);
                }

            }
        }

        if ($address_id = $object['use_addr']) {
            $address = new Address($address_id);
            $result['address'][$address_id] = Utils::extractOrm($address);
            $result['address'][$address_id]['line_view_short'] = $address->getLineView(false);
            $result['address'][$address_id]['line_view_full'] = $address->getLineView(true);
        }

        if ($warehouse_id = $object['warehouse']) {
            $result['warehouse'][$warehouse_id] =
                Utils::extractOrm(new WareHouse($warehouse_id));
        }

        if ($delivery_id = $object['delivery']) {
            $delivery = new Delivery($delivery_id);
            $delivery_type = $delivery->getTypeObject();
            $result['delivery'][$delivery_id] = Utils::extractOrm($delivery) + [
                'has_pvz' => $delivery_type->hasPvz(),
                'has_orders' => ($delivery_type instanceof InterfaceDeliveryOrder)
            ];
        }

        if ($payment_id = $object['payment']) {
            $payment = $object->getPayment();
            if ($payment->hasDocs()){ //Если есть документы для оплаты
                $payment->getPropertyIterator()->append([
                    'docs' => new ArrayList([
                        'description' => t('Список документов'),
                        'appVisible' => true
                    ]),
                ]);
                $type_object = $payment->getTypeObject();
                $docs = [];
                foreach ($type_object->getDocsName() as $key=>$doc){
                    $docs[] = [
                        'title' => $doc['title'],
                        'link' => $type_object->getDocUrl($key, true),
                    ];
                }
                $payment['docs'] = $docs;
            }
            $result['payment'][$payment_id] = Utils::extractOrm($payment);
        }

        if ($detail) {
            if ($transaction = $object->getOrderTransaction())
            {
                $actions = $transaction->getPayment()->getTypeObject()->getAvailableTransactionActions($transaction, $object);
                $title = implode(' - ', [
                    '№'.$transaction['id'],
                    $transaction['__status']->textView()
                ]);

                $result['order']['last_pay_transaction'] = [
                    'id' => $transaction['id'],
                    'title' => $title,
                    'changelog' => [],
                    'actions' => [],
                ];

                foreach($transaction->getChangeLogs() as $log_item) {
                    $result['order']['last_pay_transaction']['changelog'][] = [
                        'datetime' => $log_item['date'],
                        'change' => $log_item['change'],
                    ];
                }

                foreach($actions as $action) {
                    $result['order']['last_pay_transaction']['actions'][] = [
                        'title' => $action->getTitle(),
                        'action' => $action->getAction(),
                        'css_class' => $action->getCssClass(),
                        'confirm_text' => $action->getConfirmText(),
                    ];
                }
            }
        }

        if ($partner_id = $object['partner_id']) {
            $result['partner'][$partner_id] =
                Utils::extractOrm(new Partner($partner_id));
        }

        return $result;
    }

    /**
     * Возвращает доступное действие для транзакции
     *
     * @param $transaction
     * @return array
     */
    static function getActionInfo($transaction): array
    {
        $action_info = [
            'action' => false,
            'confirm_text' => null,
            'title' => null,
        ];

        if (!$transaction->checkSign()) {
            $action_info['title'] = t('Неверная подпись');
        }else {
            $payment_type = $transaction->getPayment()->getTypeObject();
            if ($transaction->personal_account && !$payment_type->canOnlinePay() && $transaction->status == 'new' && $transaction->order_id == 0) {
                $action_info['action'] = 'setTransactionSuccess';
                $action_info['confirm_text'] = t('Вы действительно желаете начислить средства по данной операции?');
                $action_info['title'] = t('Начислить средства');
            }
            if (!$payment_type->canOnlinePay() && $transaction->status == 'new' && $transaction->order_id > 0) {
                $action_info['action'] = 'setTransactionSuccess';
                $action_info['confirm_text'] = t('Вы действительно желаете оплатить заказ?');
                $action_info['title'] = t('Оплатить заказ');
            }
            if ($transaction->status == 'success' && $transaction->cost > 0 && ($transaction->receipt == 'no_receipt' || $transaction->receipt == 'fail')) {
                $action_info['action'] = 'sendReceipt';
                $action_info['confirm_text'] = t('Вы действительно желаете выбить чек по данной операции?');
                $action_info['title'] = t('Выбить чек');
            }
            if ($transaction->status == 'success' && $transaction->receipt == 'receipt_success' && $transaction->isPossibleRefundReceipt()) {
                $action_info['action'] = 'sendRefundReceipt';
                $action_info['confirm_text'] = t('Вы действительно желаете выбить чек возврата по данной операции?');
                $action_info['title'] = t('Сделать чек возврата');
            }
        }

        return $action_info;
    }

    /**
     * Возвращает схему отображения сведений о заказе в мобильном приложении для администраторов и курьеров.
     * Схема разработана точно под возможности приложения и соответственно не может дорабатываться только на стороне сайта
     *
     * @param Order $order Заказ
     * @return array
     */
    public static function getOrderViewScheme($order)
    {
        //Если для какого-либо поля нет своей настройки прав, проверяем общее право на изменение заказа
        $default_right_for_update = Rights::hasRight('shop', ModuleRights::RIGHT_UPDATE);
        $right_delivery_changing = Rights::hasRight('shop', ModuleRights::RIGHT_DELIVERY_CHANGING);
        $right_information_reading = Rights::hasRight('shop', ModuleRights::RIGHT_INFORMATION_READING);

        $show_cancel_block = in_array($order['status'], UserStatusApi::getStatusesIdByType(UserStatus::STATUS_CANCELLED));

        return [
            'status' => [
                'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_STATUS_CHANGING),
            ],
            'triggerCartChange' => [
                'visible' => true,
                'editable' => $default_right_for_update,
            ],
            'cargos' => [
                'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CARGO_READING),
                'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_CARGO_CHANGING),
            ],
            'cart' => [
                'rights' => [
                    'addProduct' => Rights::hasRight('shop', ModuleRights::RIGHT_PRODUCTS_ADD),
                    'editProduct' => Rights::hasRight('shop', ModuleRights::RIGHT_PRODUCTS_CHANGING),
                    'removeProduct' => Rights::hasRight('shop', ModuleRights::RIGHT_PRODUCTS_DELETE),

                    'addDiscount' => Rights::hasRight('shop', ModuleRights::RIGHT_DISCOUNT_ADD),
                    'removeDiscount' => Rights::hasRight('shop', ModuleRights::RIGHT_DISCOUNT_DELETE),
                ],
            ],
            'blocks' => [
                [
                    'title' => t('Параметры отмены заказа'),
                    'type' => 'block-standard',
                    'wide' => true,
                    'visible' => $show_cancel_block,
                    'fields' => [
                        [
                            'title' => t('Выбить чек возврата'),
                            'key' => 'create_refund_receipt',
                            'type' => 'boolean',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_TRANSACTION_ACTIONS),
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_TRANSACTION_ACTIONS),
                        ],
                        [
                            'title' => t('Причина отклонения заказа'),
                            'key' => 'substatus',
                            'type' => 'select',
                            'visible' => true,
                            'editable' => $default_right_for_update,
                            'options' => $show_cancel_block ? SubStatusApi::staticSelectList([0 => t('Не выбрано')]) : []
                        ],
                    ]
                ],
                [
                    'title' => t('Покупатель'),
                    'type' => 'block-standard',
                    'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CUSTOMER_READING),
                    'fields' => [
                        [
                            'title' => t('Покупатель'),
                            'key' => 'user_id',
                            'type' => 'client',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_CUSTOMER_CHANGING),
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CUSTOMER_FULLNAME_READING),
                        ],
                        [
                            'title' => t('E-mail'),
                            'key' => 'user_email',
                            'type' => 'client-email',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CUSTOMER_EMAIL_READING),
                        ],
                        [
                            'title' => t('Телефон'),
                            'key' => 'user_phone',
                            'type' => 'client-phone',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CUSTOMER_PHONE_READING),
                        ],
                        [
                            'title' => t('Дополнительные сведения'),
                            'type' => 'client-extra-data',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CUSTOMER_USERFIELD_READING),
                        ],
                    ]
                ],
                [
                    'title' => t('Информация'),
                    'type' => 'block-standard',
                    'visible' => $right_information_reading,
                    'fields' => [
                        [
                            'title' => t('Комментарий администратора'),
                            'key' => 'admin_comments',
                            'type' => 'textarea',
                            'editable' => $default_right_for_update,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_ADMIN_COMMENT_READING),
                        ],
                        [
                            'title' => t('Комментарий клиента'),
                            'key' => 'comments',
                            'type' => 'textarea',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_USER_COMMENT_READING),
                        ],
                        [
                            'title' => t('Последнее обновление'),
                            'key' => 'dateofupdate',
                            'type' => 'datetime',
                            'showIfExists' => true,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_LAST_UPDATE_READING),
                        ],
                        [
                            'title' => t('Дата отгрузки'),
                            'key' => 'shipment_date',
                            'type' => 'datetime',
                            'editable' => $default_right_for_update,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_SHIPMENT_DATE_READING),
                        ],
                        [
                            'title' => t('Платформа, на которой создан заказ'),
                            'key' => 'creator_platform_title',
                            'type' => 'string',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CREATE_PLATFORM_READING),
                        ],
                        [
                            'title' => t('Менеджер заказа'),
                            'key' => 'manager_user_id',
                            'editable' => $default_right_for_update,
                            'type' => 'user',
                            'data' => [
                                'filter' => [
                                    'is_manager' => 1
                                ]
                            ],
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_MANAGER_READING),
                        ]
                    ]
                ],
                [
                    'title' => t('Доставка'),
                    'type' => 'block-standard',
                    'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_DELIVERY_READING),
                    'fields' => [
                        [
                            'title' => t('Адрес'),
                            'type' => 'address',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_ADDRESS_CHANGING),
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_ADDRESS_READING),
                        ],
                        [
                            'title' => t('Способ доставки'),
                            'type' => 'delivery',
                            'editable' => $right_delivery_changing,
                            'visible' => true,
                        ],
                        [
                            'title' => t('Пункт выдачи'),
                            'type' => 'pvz',
                            'editable' => $right_delivery_changing,
                            'visible' => true,
                        ],
                        [
                            'title' => t('Курьер'),
                            'type' => 'user',
                            'key' => 'courier_id',
                            'editable' => $right_delivery_changing,
                            'data' => [
                                'filter' => [
                                    'is_courier' => 1
                                ]
                            ],
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_COURIER_READING),
                        ],
                        [
                            'title' => t('Трек-номер'),
                            'key' => 'track_number',
                            'type' => 'string',
                            'editable' => $right_delivery_changing,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_TRACK_NUMBER_READING),
                        ],
                        [
                            'title' => t('Контактное лицо'),
                            'key' => 'contact_person',
                            'type' => 'string',
                            'editable' => $right_delivery_changing,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_CONTACT_PERSON_READING),
                        ],
                        [
                            'title' => t('Склад'),
                            'key' => 'warehouse',
                            'type' => 'warehouse',
                            'editable' => $right_delivery_changing,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_WAREHOUSE_READING),
                        ],
                        [
                            'title' => t('Заказы на доставку'),
                            'key' => 'delivery-orders',
                            'type' => 'delivery-orders',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_DELIVERY_CHANGING),
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_DELIVERY_READING),
                        ],
                    ]
                ],
                [
                    'title' => t('Оплата'),
                    'type' => 'block-standard',
                    'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_PAY_READING),
                    'fields' => [
                        [
                            'title' => t('Способ оплаты'),
                            'key' => 'payment',
                            'type' => 'payment',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_PAY_CHANGING),
                            'visible' => true,
                        ],
                        [
                            'title' => t('Заказ оплачен?'),
                            'key' => 'is_payed',
                            'type' => 'boolean',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_PAY_CHANGING),
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_IS_PAY_READING),
                        ],
                        [
                            'title' => t('Документы для оплаты'),
                            'type' => 'payment-docs',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_PAY_DOCS_READING),
                        ],
                        [
                            'title' => t('Выбранный метод оплаты'),
                            'type' => 'payment-recurring',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_TRANSACTION_ACTIONS),
                            'visible' => true,
                        ],
                        [
                            'title' => t('Транзакция на оплату'),
                            'type' => 'payment-transaction',
                            'key' => 'lastPayTransaction',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_TRANSACTION_ACTIONS),
                            'visible' => true,
                        ],
                    ]
                ],
                [
                    'title' => t('Дополнительная информация'),
                    'type' => 'block-standard',
                    'visible' => $right_information_reading,
                    'fields' => [
                        [
                            'title' => t('Доход от заказа'),
                            'key' => 'profit',
                            'type' => 'money',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_PROFIT_READING),
                        ],
                        [
                            'title' => t('Дополнительная информация'),
                            'type' => 'extra-info',
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_EXTRA_INFO_READING),
                        ],
                        [
                            'title' => t('Произвольные поля заказа'),
                            'key' => 'additional_fields',
                            'valueKey' => 'userfields_arr',
                            'type' => 'user-fields',
                            'editable' => $default_right_for_update,
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_USERFIELDS_READING),
                        ],
                        [
                            'title' => t('Отгрузки'),
                            'key' => 'shipments',
                            'type' => 'shipments',
                            'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_MAKE_SHIPMENT),
                            'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_MAKE_SHIPMENT),
                        ]
                    ]
                ],
                [
                    'title' => t('Текст для покупателя'),
                    'hint' => t('Будет виден покупателю на странице просмотра заказа'),
                    'type' => 'block-user-text',
                    'key' => 'user_text',
                    'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_USER_TEXT_CHANGING),
                    'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_USER_TEXT_READING),
                ],
                [
                    'title' => t('Документы'),
                    'hint' => t('Будут видны покупателю на странице просмотра заказа'),
                    'type' => 'block-documents',
                    'wide' => true,
                    'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_DOCUMENTS_PRINTING),
                    'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_DOCUMENTS_PRINTING),
                ],
                [
                    'title' => t('Прикрепленные файлы'),
                    'hint' => t('Будут видны покупателю на странице просмотра заказа'),
                    'type' => 'block-files',
                    'wide' => true,
                    'editable' => Rights::hasRight('shop', ModuleRights::RIGHT_FILES_CHANGING),
                    'visible' => Rights::hasRight('shop', ModuleRights::RIGHT_FILES_CHANGING),
                ]
            ]
        ];
    }

    /**
     * Удаляет спец.символы, отсканированные в приложении в dataMatrix
     *
     * @param $datamatrix
     * @return array|string|string[]
     */
    static function prepareMobileDataMatrix($datamatrix)
    {
        return str_replace([chr(232), chr(29)], '', $datamatrix);
    }

    /**
     * Возвращает массив статусов для предзаказов
     *
     * @return array[]
     */
    static function getReservationStatuses(): array
    {
        $status_titles = Reservation::getStatusTitles();
        return [
            [
                'title' => $status_titles[Reservation::STATUS_OPEN],
                'id' => Reservation::STATUS_OPEN,
                'color' => Reservation::STATUS_COLOR_OPEN,
            ],
            [
                'title' => $status_titles[Reservation::STATUS_CLOSE],
                'id' => Reservation::STATUS_CLOSE,
                'color' => Reservation::STATUS_COLOR_CLOSE,
            ],
        ];
    }
}