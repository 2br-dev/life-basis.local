<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Main\Model\StatisticEvents;
use RS\Event\Manager as EventManager;
use Shop\Model\AddressApi;
use Shop\Model\ApiUtils;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\PaymentApi;

/**
* Реализует первый шаг оформления заказа. Этап отправления адреса и оставление контактов.
*/
class SetAddress extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;
        
    protected
        $token_require = false;
        
    public
        /** @var OrderApi */
        $order_api,
        /** @var DeliveryApi */
        $delivery_api,
        /** @var PaymentApi */
        $payment_api,
        /** @var AddressApi */
        $address_api,
        /** @var Order */
        $order,
        $shop_config;
        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->delivery_api = new DeliveryApi();
        $this->payment_api = new PaymentApi();
        $this->address_api = new AddressApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно    
        $this->shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
    }
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Отправка данных')
        ];
    }
    
    /**
    * Сохраняет новый адрес принадлежащий пользователю и заказу
    * 
    */
    private function saveNewAddress()
    {
        $address = new \Shop\Model\Orm\Address();
        $address->getFromArray($this->order->getValues(), 'addr_');
        $address['user_id'] = $this->token ? $this->token->getUser()->id : \RS\Application\Auth::getCurrentUser()->id;  
                 
        if ($address->insert()) {
            $this->order->setUseAddr($address['id']);
        }
    }

    /**
    * Возвращает список доставок по текущему оформляемому заказу из сессии
    *
    * @param string sortn - сортировка элементов
    *
    * @return array
    */
    private function getDeliveryListByCurrentOrder($sortn)
    {
        return \Shop\Model\ApiUtils::getOrderDeliveryListSection($this->token, $this->order, $sortn);
    }
    
    /**
    * Возвращает список оплат по текущему оформляемому заказу из сессии
    * 
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    private function getPaymentListByCurrentOrder($sortn)
    {
        return \Shop\Model\ApiUtils::getOrderPaymentListSection($this->token, $this->order, $sortn);
    }

    /**
     * Устанавливает адрес при оформлении заказа
     *
     * @param null $city_id ID города
     * @param null $city Название города
     * @param null $region_id ID региона
     * @param null $region Название региона
     * @param null $country_id ID страны
     * @param null $country Название страны
     * @param null $address Адрес
     * @param null $zipcode Индекс
     * @param null $street Улица
     * @param null $house Дом
     * @param null $block Корпус
     * @param null $apartment Квартира
     * @param null $entrance Подъезд
     * @param null $entryphone Домофон
     * @param null $floor Этаж
     * @param null $subway Станция метро
     * @param null $token Авторизиционный токен
     *
     *
     * @return array Возвращает пустой массив ошибок, возвращает массив доставок для установленного адреса
     * @throws \RS\Exception
     * @example POST /api/methods/checkout.setAddress?city=Екатеринбург&city_id=1031&country=Россия&country_id1&regionСвердловская область&region_id=6
     *
     * Ответ:
     * <pre>
     *  {
     *      "response": {
     *          "success": true,
     *          "errors": [],
     *          "delivery": {
     *              "errors": [],
     *              "list": []
     *         }
     *  }
     * </pre>
     */
    protected function process(
        $city_id = null,
        $city = null,
        $region_id = null,
        $region = null,
        $country_id = null,
        $country = null,
        $address = null,
        $zipcode = null,
        $street = null,
        $house = null,
        $block = null,
        $apartment = null,
        $entrance = null,
        $entryphone = null,
        $floor = null,
        $subway = null,
        $token = null
    )
    {
        $response['response']['success'] = false;
              
        //Если корзины на этот момент уже не существует.
        if ( $this->order['expired'] || !$this->order->getCart() ){ 
            $errors[] = "Корзина заказа пуста. Необходимо наполнить корзину.";
            $response['response']['errors'] = $errors;
            $response['response']['error_status'] = 2;
            return $response;
        } 
        
        $cart_data = $this->order['basket'] ? $this->order->getCart()->getCartData() : null;
        if ($cart_data === null || !count($cart_data['items']) || $cart_data['has_error'] || $this->order['expired']) {
            //Если корзина пуста или заказ уже оформлен или имеются ошибки в корзине, то выполняем redirect на главную сайта
            $errors[] = "Корзина заказа пуста, истекла сессия или в ней имеются ошибки. Оформите корзину заново.";
            $response['response']['errors']  = $errors;
            $response['response']['error_status'] = 3;
            return $response;
        }

        $order_address = new Address();
        $order_address->city_id = $city_id;
        $order_address->city = $city;
        $order_address->region_id = $region_id;
        $order_address->region = $region;
        $order_address->country_id = $country_id;
        $order_address->country = $country;
        $order_address->address = $address;
        $order_address->zipcode = $zipcode;
        $order_address->street = $street;
        $order_address->house = $house;
        $order_address->block = $block;
        $order_address->apartment = $apartment;
        $order_address->entrance = $entrance;
        $order_address->entryphone = $entryphone;
        $order_address->floor = $floor;
        $order_address->subway = $subway;
        $this->order->setAddress($order_address);

        $errors = $this->order->getErrors();
        $response['response']['errors']  = $errors;
        if (!$this->order->hasError()){
            $response['response']['success'] = true;
            //Данные для следующего шага
            if (!$this->shop_config['hide_delivery']){
                $this->order->getAddress(false); //Сбросим кэш адреса
                $response['response']['delivery'] = $this->getDeliveryListByCurrentOrder('sortn');
            }else{ //Подтверждение
                $response['response']['cartdata'] = ApiUtils::fillProductItemsData($this->order);
            }

            EventManager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_FILL_ADDRESS]);
        }

        return $response;
    }
}
