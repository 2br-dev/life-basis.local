<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;

use Catalog\Model\UnitApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use Main\Model\StatisticEvents;
use RS\Config\Loader;
use RS\Event\Manager;
use RS\Exception;
use RS\Helper\CustomView;
use RS\Orm\Type\ArrayList;
use RS\Orm\Type\MixedType;
use Shop\Model\ApiUtils;
use Shop\Model\App\InterfaceOrderCreation;
use Shop\Model\Cart;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;

/**
 * Реализует третий шаг оформления заказа. Этап отправления подтверждения заказа
 */
class Confirm extends AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1;

    protected
        $token_require = false;

    public
        /**
         * @var OrderApi
         */
        $order_api,
        /**
         * @var Order
         */
        $order,
        $shop_config;

    function __construct()
    {
        parent::__construct();
        $this->order     = Order::currentOrder();
        $this->order_api = new OrderApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно
        $this->shop_config = Loader::byModule('shop'); //Конфиг магазина
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
     * Реализует третий шаг оформления заказа. Этап подтверждения заказа
     *
     * @param integer $iagree согласие с условиями продаж. Нужно только когда включен показ лицензионного соглашения в настроках модуля магазин.
     * @param integer $comments комментарий к заказу.
     *
     * @example POST /api/methods/checkout.confirm
     *
     * Ответ:
     * <pre>
     * {
     *        "response": {
     *            "success" : false,
     *            "errors" : ['Ошибка'],
     *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста)
     *        }
     *    }
     * </pre>
     *
     * @return array Возращает, либо пустой массив ошибок, если успешно
     */
    protected function process($token = null, $iagree = null, $comments = null)
    {
        $errors = [];
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

        $this->order->clearErrors();
        if ($this->shop_config->require_license_agree && !$iagree) {
            $this->order->addError(t('Подтвердите согласие с условиями предоставления услуг'));
        }

        $sysdata = ['step' => 'confirm'];
        $work_fields = $this->order->useFields($sysdata + $_POST);

        $this->order->setCheckFields($work_fields);
        if (!$this->order->hasError() && $this->order->checkData($sysdata, null, null, $work_fields)) {
            $this->order['is_payed'] = 0;
            $this->order['delivery_new_query'] = 1;
            $this->order['payment_new_query'] = 1;
            $this->order['is_mobile_checkout'] = 1; //Выгружен из мобильного приложения, старый флаг
            $this->fillCreatorPlatform($this->order);

            // Событие для модификации корзины (вызывается повторно непосредственно перед сохранением заказа)
            Manager::fire('checkout.confirm', [
                'order' => $this->order,
                'cart' => $this->order->getCart()
            ]);

            //Создаем заказ в БД
            if ($this->order->insert()) {
                // Фиксация события "Подтверждение заказа" для статистики
                Manager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_CONFIRM_ORDER]);

                $this->order['expired'] = true; //заказ уже оформлен. больше нельзя возвращаться к шагам.
                Cart::currentCart()->clean(); //Очищаем корзиу
            }
        }


        $errors = $this->order->getErrors();
        $response['response']['errors']  = $errors;
        if (!$this->order->hasError()){
            $response['response']['success'] = true;
            //Отправим сведения по заказу
            $response['response']['order']                     = Utils::extractOrm($this->order);
            $response['response']['order']['can_online_pay']   = $this->order->canOnlinePay();
            $response['response']['order']['dateof_timestamp'] = strtotime($this->order['dateof']); //Дата цифрой
            $response['response']['order']['dateof']           = $this->order['dateof']; //Дата dd.mm.YYYY HH:ii:ss
            $response['response']['order']['dateof_date']      = date('d.m.Y', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY
            $response['response']['order']['dateof_datetime']  = date('d.m.Y H:i', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY HH:ii
            $response['response']['order']['dateof_iso']       = date('c', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY

            //Дополнительные секции
            if ($this->order['payment']){
                $payment = $this->order->getPayment();
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
                $response['response']['payment']   = Utils::extractOrm($payment);
            }
            if ($this->order['delivery']){
                $response['response']['delivery']  = Utils::extractOrm($this->order->getDelivery());
            }
            if ($this->order['use_addr']){
                $response['response']['address']   = Utils::extractOrm($this->order->getAddress());
            }
            if ($this->order['warehouse']){
                $response['response']['warehouse'] = Utils::extractOrm($this->order->getWarehouse());
            }
            //Если есть файлы привязанные к заказу
            if ($files = $this->order->getFiles()) {
                $this->order->getPropertyIterator()->append([
                    'files' => new MixedType([
                        'description' => t('Файлы прикреплённые к заказу'),
                        'appVisible' => true
                    ]),
                ]);
                $order_files = [];
                foreach ($files as $file){
                    $order_files[] = [
                        'title' => $file['name'],
                        'link'  => $file->getUrl(true)
                    ];
                }
                $this->order['files'] = $order_files;
            }
            $response['response']['user']         = Utils::extractOrm($this->order->getUser());
            $response['response']['status']       = Utils::extractOrm($this->order->getStatus());

        }

        return $response;
    }

    /**
     * Реализует третий шаг оформления заказа. Этап подтверждения заказа
     * ---
     * Изменения во второй версии метода:
     * - Не требуется передача параметров iagree и comments.
     * - Сохранение нового адреса пользователя происходит при создании заказа
     *
     * @param null $token Авторизационный токен
     *
     * @example POST /api/methods/checkout.confirm
     *
     * Ответ:
     * Ответ:
     * <pre>
     * {
     *        "response": {
     *            "success" : false,
     *            "errors" : ['Ошибка'],
     *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста)
     *        }
     *    }
     * </pre>
     *
     * @return mixed
     * @throws Exception
     */
    protected function processVer2($token = null)
    {
        $errors = [];
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

        $this->order->clearErrors();

        $sysdata = ['step' => 'confirm'];
        $work_fields = $this->order->useFields($sysdata + $_POST);

        $this->order->setCheckFields($work_fields);
        if (!$this->order->hasError() && $this->order->checkData($sysdata, null, null, $work_fields)) {
            $this->order['is_payed'] = 0;
            $this->order['delivery_new_query'] = 1;
            $this->order['payment_new_query'] = 1;
            $this->order['is_mobile_checkout'] = 1; //Выгружен из мобильного приложения
            $this->fillCreatorPlatform($this->order);

            // Событие для модификации корзины (вызывается повторно непосредственно перед сохранением заказа)
            Manager::fire('checkout.confirm', [
                'order' => $this->order,
                'cart' => $this->order->getCart()
            ]);

            // Сохраняем адрес
            if ($this->order['use_addr'] == 0) {
                $new_address = $this->order->getAddress();
                $new_address['user_id'] = $this->order->getUser()['id'];
                if ($new_address->insert()) {
                    $this->order->setUseAddr($new_address['id']);
                } else {
                    $this->order->addErrors($new_address->getNonFormErrors(), 'addr');
                    foreach ($new_address->getErrorsByForm() as $form => $errors) {
                        $this->order->addErrors($errors, "addr_$form");
                    }
                }
            }

            //Создаем заказ в БД
            if ($this->order->insert()) {
                // Фиксация события "Подтверждение заказа" для статистики
                Manager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_CONFIRM_ORDER]);

                $this->order['expired'] = true; //заказ уже оформлен. больше нельзя возвращаться к шагам.
                Cart::currentCart()->clean(); //Очищаем корзиу
            }
        }


        $errors = $this->order->getErrors();
        $response['response']['errors']  = $errors;
        if (!$this->order->hasError()){
            $this->order = ApiUtils::addOrderItems($this->order);

            $response['response']['success'] = true;
            //Отправим сведения по заказу
            $response['response']['order']                     = Utils::extractOrm($this->order);
            $response['response']['order']['can_online_pay']   = $this->order->canOnlinePay();
            $response['response']['order']['pay_url']          = $this->order->getOnlinePayUrl(true, ['rs_mobile_app' => 1]);
            $response['response']['order']['dateof_timestamp'] = strtotime($this->order['dateof']); //Дата цифрой
            $response['response']['order']['dateof']           = $this->order['dateof']; //Дата dd.mm.YYYY HH:ii:ss
            $response['response']['order']['dateof_date']      = date('d.m.Y', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY
            $response['response']['order']['dateof_datetime']  = date('d.m.Y H:i', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY HH:ii
            $response['response']['order']['dateof_iso']       = date('c', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY

            $units = [];
            if (!empty($response['response']['order']['items'])){
                foreach ($response['response']['order']['items'] as &$cartitem){ //Добавим форматированную
                    $cartitem['price_formatted'] = CustomView::cost($cartitem['price'])." ".$this->order['currency_stitle'];

                    if ($cartitem['unit_id']) $units[$cartitem['unit_id']] = true;
                }
            }

            //Загружаем единицы измерения
            if ($units) {
                $default_unit = (int)Loader::byModule($this)->default_unit;

                $unit_api = new UnitApi();
                $unit_api->setFilter('id', array_merge(array_keys($units), [$default_unit]), 'in');
                $unit_objects = Utils::extractOrmList($unit_api->getList(), 'id');

                //Загружаем единицу измерения по умолчанию.
                if ($default_unit && isset($units[0])) {
                    $unit_objects[0] = $unit_objects[$default_unit];
                }
                $response['response']['unit'] = $unit_objects;
            }

            if  ($response['response']['order']['totalcost']) {
                $response['response']['order']['totalcost_formatted'] = CustomView::cost($response['response']['order']['totalcost'])." ".$this->order['currency_stitle'];
            }

            //Дополнительные секции
            if ($this->order['payment']){
                $payment = $this->order->getPayment();
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
                $response['response']['payment']   = Utils::extractOrm($payment);
            }
            if ($this->order['delivery']){
                $response['response']['delivery']  = Utils::extractOrm($this->order->getDelivery());
            }
            if ($this->order['use_addr']){
                $response['response']['address']   = Utils::extractOrm($this->order->getAddress());
            }
            if ($this->order['warehouse']){
                $response['response']['warehouse'] = Utils::extractOrm($this->order->getWarehouse());
            }
            //Если есть файлы привязанные к заказу
            if ($files = $this->order->getFiles()) {
                $this->order->getPropertyIterator()->append([
                    'files' => new MixedType([
                        'description' => t('Файлы прикреплённые к заказу'),
                        'appVisible' => true
                    ]),
                ]);
                $order_files = [];
                foreach ($files as $file){
                    $order_files[] = [
                        'title' => $file['name'],
                        'link'  => $file->getUrl(true)
                    ];
                }
                $this->order['files'] = $order_files;
            }
            $response['response']['user']         = Utils::extractOrm($this->order->getUser());
            $response['response']['status']       = Utils::extractOrm($this->order->getStatus());

            $response['response']['cartdata'] = ApiUtils::fillProductItemsData();

        }

        return $response;
    }

    /**
     * Заполняет информацию о платформе-создателе заказа
     *
     * @param Order $order
     */
    protected function fillCreatorPlatform($order)
    {
        if ($this->token) {
            $app = $this->token->getApp();
            if ($app instanceof InterfaceOrderCreation) {
                $platform_id = $app->getCreatorPlatformId();
                $app->addOrderExtraData($this, $order);
            }
        }
        $order['creator_platform_id'] = $platform_id ?? Order::CREATOR_PLATFORM_API;
    }
}