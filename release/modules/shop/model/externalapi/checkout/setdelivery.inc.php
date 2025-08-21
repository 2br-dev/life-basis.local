<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use Catalog\Model\WareHouseApi;
use Shop\Model\AddressApi;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;
use Shop\Model\PaymentApi;

/**
* Реализует первый шаг оформления заказа. Этап отправления выбора доставки.
*/
class SetDelivery extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;
        
    protected
        $token_require = false,
        $register_validator,
        $address_validator;
        
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
     * Устанавливает доставку при оформлении заказа
     *
     * @param $delivery_id ID способа доставки
     * @param null $warehouse_id ID склада
     * @param null $delivery_extra Дополнительные параметры к доставке
     * @param array $additional_fields Дополнительные поля к доставке (напримет адрес, индекс)
     * @param null $use_addr ID сохраненного адреса пользователя
     * @param null $comment Комментарий к заказу
     * @param null $token Авторизиционный токен
     *
     *
     * @example POST /api/methods/checkout.setDelivery?delivery_id=1&use_addr=1
     *
     * Ответ:
     * <pre>
     *  {
     *      "response": {
     *          "success": true,
     *          "errors": []
     *      }
     *  }
     * </pre>
     *
     * @return array Возращает пустой массив ошибок, если успешно
     * @throws \RS\Exception
     */
    protected function process($delivery_id, $warehouse_id = null, $delivery_extra = null, $additional_fields = [], $use_addr = null, $comment = null, $token = null)
    {
        $response['response']['success'] = false;
        $response['response']['errors'] = [];

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

        $this->order->clearErrors();

        if ($warehouse_id) {
            $this->order['warehouse'] = $warehouse_id;
        }else {
            $this->order['warehouse'] = ($this->order->getStockAffiliateWarehouse()) ? $this->order->getStockAffiliateWarehouse()['id'] : WareHouseApi::getDefaultWareHouse()['id'];
        }

        $this->order['__payment']->removeAllCheckers();
        $this->order->delivery = $delivery_id;

        if ($use_addr) {
            $this->order->setUseAddr($use_addr);
        }

        if ($additional_fields && !$use_addr) {
            $order_address = $this->order->getAddress();
            foreach ($additional_fields as $field) {
                foreach ($field as $key => $value) {
                    $order_address[$key] = $value;
                }
            }
            $this->order->setAddress($order_address);
        }

        if ($delivery_extra) {
            $this->order->addExtraKeyPair('delivery_extra', $delivery_extra);
        }

        if ($comment) {
            $this->order->comments = $comment;
        }

        $this->order->validate();

        if (!$this->order->hasError()){
            $response['response']['success'] = true;
        }else {
            $response['response']['errors'] = $this->order->getErrors();
        }

        return $response;
    }
}
