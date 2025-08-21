<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use Catalog\Model\WareHouseApi;
use ExternalApi\Model\Validator\ValidateArray;
use Main\Model\StatisticEvents;
use \ExternalApi\Model\Exception as ApiException;
use RS\Application\Auth;
use Shop\Model\AddressApi;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\PaymentApi;

/**
* Реализует второй шаг оформления заказа. Этап выбора способа оплаты.
*/
class SetPayment extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
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
     * Устанавливает оплату при оформлении заказа
     *
     * @param $payment_id ID способа оплаты
     * @param null $token Авторизиционный токен
     *
     *
     * @example POST /api/methods/checkout.setPayment?payment_id=1
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
    protected function process($payment_id, $token = null)
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

        $this->order->payment = $payment_id;

        if (!$this->order->hasError()){
            $response['response']['success'] = true;
        }else {
            $response['response']['errors'] = $this->order->getErrors();
        }

        return $response;
    }
}
