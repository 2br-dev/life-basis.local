<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;

/**
* Реализует первоначальную инициализацию перед началом оформления заказа. 
*/
class Init extends AbstractAuthorizedMethod
{
    const RIGHT_INIT = 1;
        
    protected
        $token_require = false;
        
        
    public
        /**
        * @var \Shop\Model\Orm\OrderApi
        */
        $order_api,
        /**
        * @var \Shop\Model\Orm\Order
        */
        $order,
        $shop_config;

        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
        $this->order->clearErrors(); //Очистим ошибки предварительно    
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
            self::RIGHT_INIT => t('Инициализация оформления заказа')
        ];
    }



    /**
    * Реализует первоначальную инициализацию перед началом оформления заказа.
    * ---
    * Обязательный метод перед checkout.address.
    *
    * @param string $token Авторизационный токен
    *
    * @example POST /api/methods/checkout.init
    *
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : ['Ошибка']
    *        }
    *    }
    * </pre>
    *
    * @return array Возращает, либо пустой массив ошибок, если инициализация успешно пройдена, либо массив ошибок и success false
    */
    protected function process($token = null)
    {
        $this->order->clear();
                 
        //Замораживаем объект "корзина" и привязываем его к заказу
        $frozen_cart = \Shop\Model\Cart::preOrderCart(null);
        $frozen_cart->splitSubProducts();
        $frozen_cart->mergeEqual();
        
        $this->order->linkSessionCart($frozen_cart);

        $this->order->setCurrency( \Catalog\Model\CurrencyApi::getCurrentCurrency() );
        
        $this->order['ip']        = $_SERVER['REMOTE_ADDR'];
        $this->order['warehouse'] = 0;
        
        $this->order['expired'] = false;
        if (!isset($this->order['use_addr'])) {
            $this->order->setDefaultAddress();
            $response['response']['address']   = \ExternalApi\Model\Utils::extractOrm($this->order->getAddress());
        }

        if ($user = $this->token->getUser()) {
            if ($user->id > 0) {
                $this->order->setUserId($user->id);
            }

            if (!$this->shop_config['hide_delivery']){ // Доставки и оплаты
                $this->order->getAddress(false); //Сбросим кэш адреса
                $response['response']['address']   = \ExternalApi\Model\Utils::extractOrm($this->order->getAddress());
                $response['response']['delivery']   = \Shop\Model\ApiUtils::getOrderDeliveryListSection($this->token, $this->order, 'sortn');
            }
        }

        if ($errors = $this->order->getErrors()) {
            $response['response']['success'] = false;
            $response['response']['errors']  = $errors;
        }else {
            $response['response']['success'] = true;
        }

        return $response;
    }
}