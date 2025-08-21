<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\DeliveryType\Cdek;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Orm\Product;
use Catalog\Model\ProductDimensions;
use Main\Model\Requester\ExternalRequest;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\HashStore\Api as HashStoreApi;
use RS\Http\Request as HttpRequest;
use RS\Orm\Request as OrmRequest;
use RS\Router\Manager as RouterManager;
use Shop\Model\Cart;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\Cdek2;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Exception as ShopException;
use Shop\Model\Log\LogDeliveryCdek;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Delivery\CdekRegion;
use Shop\Model\Orm\DeliveryOrder;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\Region;
use Shop\Model\Orm\Tax;
use Shop\Model\PaymentType\AbstractType;
use Shop\Model\TaxApi;

class CdekApi
{
    const DEVELOPER_KEY = 'r5$E7UPuZG:%X$r0j8N-5bUR~go$mKFr'; // Передаётся в некоторых запросах. Выдан СДЭК-ом 28.09.2017.
    const URL = "https://api.cdek.ru/v2/";
    const TEST_URL = 'https://api.edu.cdek.ru/v2/';
    const EXTERNAL_REQUEST_SOURCE_ID = 'delivery_cdek_api';
    const DEFAULT_TIMEOUT = 20;
    const DELIVERY_MODES_FROM_PVZ = [3, 4, 7];
    const DELIVERY_MODES_TO_PVZ = [2, 4, 6, 7];
    const TARIFF_PRIORITY_SORT = 'sort';
    const TARIFF_PRIORITY_PRICE = 'price';
    const TARIFF_PRIORITY_TIME = 'time';
    const DELIVERY_ORDER_EXTRA_KEY_CALL_COURIER_ID = 'call_courier_id';
    const EXCEPTION_ERROR_CODES = 'cdek_error_codes';
    const TEST_ACCOUNT = 'wqGwiQx0gg8mLtiEKsUinjVSICCjtTEP';
    const TEST_SECURE_PASSWORD = 'RmAmgvSgSl1yirlz9QupbzOJVqhCxcP5';

    protected $account = '';
    protected $secure_password = '';
    protected $auth_token;
    protected $test_mode = false;
    protected $timeout = self::DEFAULT_TIMEOUT;
    protected $address_from;
    protected $pvz_from;
    protected $days_before_send = 0;
    protected $tariff_list = [];
    protected $tariff_priority = self::TARIFF_PRIORITY_SORT;
    protected $type_object;
    protected $log;

    function __construct(string $account = '', string $secure_password = '')
    {
        $this->log = LogDeliveryCdek::getInstance();
        $this->setAuthorization($account, $secure_password);
    }

    /**
     * Возвращает список ПВЗ
     *
     * @param Address $address адрес
     * @param string $points_type тип точек на карте (ALL (Все),PVZ (Офисы),POSTOMAT (Постоматы))
     * @return Pvz[]
     * @throws ShopException
     */
    public function getPvzList(Address $address, $points_type = null)
    {
        $this->log->write(t('Запрос списка ПВЗ'), LogDeliveryCdek::LEVEL_PVZ);
        $params = [
            'type' => $points_type ?? $this->getTypeObject()->getOption('delivery_points_type', 'ALL'),
        ];

        if ($city_code = $this->findLocationIdByAddress($address)) {
            if ($city_code == 44 && $this->getTypeObject()->getOption('show_pvz_from_region_in_moscow')) {
                //Для Москвы, если включена опция, то отображаем ПВЗ со всего региона
                $params['region_code'] = 81;
            } else {
                $params['city_code'] = $city_code;
            }
        } elseif ($address->getCity()['zipcode']) {
            $params['postal_code'] = $address->getCity()['zipcode'];
        } else {
            return [];
        }

        $this->log->write(t('Параметры запроса') . "\n" . var_export($params, true), LogDeliveryCdek::LEVEL_PVZ);
        $response = $this->apiRequest(ExternalRequest::METHOD_GET,
            'deliverypoints',
            $params,
            LogDeliveryCdek::LEVEL_PVZ,
            true);

        $pvz_list = [];
        if (is_array($response) && empty($response['errors'])) {
            usort($response, function($a, $b) {
                return $a['location']['address'] <=> $b['location']['address'];
            });

            foreach ($response as $pvz_data) {
                $pvz = new \Shop\Model\DeliveryType\Helper\Pvz();

                $phones = [];
                if (isset($pvz_data['phones'])) {
                    foreach ($pvz_data['phones'] as $phone) {
                        $phones[] = $phone['number'];
                    }
                }

                $pvz->setCode($pvz_data['code']);
                $pvz->setTitle($pvz_data['name'] ?? $pvz_data['code']);
                $pvz->setCountry($pvz_data['location']['country_code']);
                $pvz->setRegion($pvz_data['location']['region']);
                $pvz->setCity($pvz_data['location']['city']);
                $pvz->setAddress($pvz_data['location']['address']);
                $pvz->setPhone(implode(', ', $phones));
                $pvz->setWorktime($pvz_data['work_time']);
                $pvz->setCoordX($pvz_data['location']['longitude']);
                $pvz->setCoordY($pvz_data['location']['latitude']);
                $pvz->setPaymentByCards($pvz_data['have_cashless']);
                $pvz->setExtra([
                    'city_code' => $pvz_data['location']['city_code'] ?? null,
                    'region_code' => $pvz_data['location']['region_code'] ?? null,
                    'country_code' => $pvz_data['location']['country_code'] ?? null,
                    'postal_code' => $pvz_data['location']['postal_code'] ?? null,
                    'type' => $pvz_data['type'] ?? null,
                    'owner_code' => $pvz_data['owner_code'] ?? null,
                ]);

                $pvz_list[] = $pvz;
            }
        }

        $this->log->write(t('Найдено %0 ПВЗ', [count($pvz_list)]), LogDeliveryCdek::LEVEL_PVZ);
        return $pvz_list;
    }

    /**
     * Запрашивает информацию о заказе
     *
     * @param string $order_id - идентификатор заказа
     * @return array
     * @throws ShopException
     */
    public function getOrderInfo(string $order_id): array
    {
        $response = $this->apiRequest(ExternalRequest::METHOD_GET, "orders/$order_id", [], LogDeliveryCdek::LEVEL_ORDER);
        return $response['entity'];
    }

    /**
     * Удаляет заказ в СДЭК
     *
     * @param DeliveryOrder $delivery_order
     * @return void
     * @throws ShopException
     */
    public function deleteOrder(DeliveryOrder $delivery_order): void
    {
        try {
            $this->apiRequest(ExternalRequest::METHOD_DELETE, "orders/{$delivery_order['external_id']}", [], LogDeliveryCdek::LEVEL_ORDER);
        } catch (ShopException $e) {
            if (!in_array('v2_similar_request_still_processed', $e->getExtraData(self::EXCEPTION_ERROR_CODES, []))
                && !in_array('v2_entity_invalid', $e->getExtraData('code', []))) {
                throw $e;
            }
        }
        $delivery_order->delete();
    }

    /**
     * Обновляет информацию о заказе в СДЭК
     *
     * @param DeliveryOrder $delivery_order
     * @return DeliveryOrder
     * @throws ShopException
     */
    public function refreshOrder(DeliveryOrder $delivery_order): DeliveryOrder
    {
        $data = $this->getOrderInfo($delivery_order['external_id']);
        if ($data) {
            $delivery_order['data'] = $data;
            $delivery_order->update();
            return $delivery_order;
        } else {
            throw new ShopException(t('Произошла непредвиденная ошибка, обратитесь в поддержку'));
        }
    }

    /**
     * Создаёт заказ в СДЭК
     *
     * @param Order $order - заказ
     * @return DeliveryOrder
     * @throws ShopException
     * @throws RSException
     */
    public function createOrder(Order $order): DeliveryOrder
    {
        $user = $order->getUser();
        $delivery_type = $this->getTypeObject();
        $payment_type = $order->getPayment()->getTypeObject();

        if (!$delivery_type->getOption('phone_from')) {
            throw new ShopException(t('Не указан телефон отправителя (Укажите его в настройках способа доставки)'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
        }
        if (!$user->getFio()) {
            throw new ShopException(t('Не указаны Ф.И.О. получателя'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
        }
        if (empty($user['phone'])) {
            throw new ShopException(t('Не указан телефон получателя'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
        }

        $calculation = $this->getPriorityTariff($order);
        if (empty($calculation)) {
            throw new ShopException(t('Нет доступных тарифов'), ShopException::ERR_DELIVERY_RESULT_ERROR);
        }

        $params = [
            'number' => $this->getOrderNumber((string)$order['order_num']),
            'tariff_code' => $calculation['tariff_code'],
            'developer_key' => self::DEVELOPER_KEY,
            /*'delivery_recipient_cost' => [
                'value' => '',
            ],*/
            'sender' => [
                'phones' => [
                    'number' => $delivery_type->getOption('phone_from', ''),
                ],
            ],
            'recipient' => [
                'name' => $user->getFio(),
                'phones' => [
                    [
                        'number' => $user['phone'],
                    ]
                ],
            ],
            'services' => (array)$delivery_type->getOption('additional_services'),
            'packages' => $this->getOrderPackages($order),
        ];

        if ($user['is_company']) {
            $params['recipient']['company'] = $user['company'];
            $params['recipient']['tin'] = $user['company_inn'];
        }

        $cash_on_delivery_option = $delivery_type->getOption('default_cash_on_delivery');
        $cash_on_delivery = 0;

        if (!$cash_on_delivery_option || $cash_on_delivery_option == 1) {
            if (!empty($order['payment'])) {
                if ($payment_type->cashOnDelivery()) {
                    $cash_on_delivery = 1;
                }
            } else {
                $cash_on_delivery = $cash_on_delivery_option;
            }
        } elseif ($cash_on_delivery_option == 2) {
            $cash_on_delivery = 1;
        }

        if ($cash_on_delivery) {
            $delivery_sum = 0;
            foreach ($order->getCart()->getCartItemsByType(Cart::TYPE_DELIVERY) as $order_item) {
                $delivery_sum += $order_item['price'] - $order_item['discount'];
            }
            $params['delivery_recipient_cost'] = [
                'value' => $delivery_sum,
            ];
        }

        $params = array_merge($params, $this->getFromLocationData($calculation));
        $params = array_merge($params, $this->getToLocationData($calculation, $order));


        $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'orders', $params, LogDeliveryCdek::LEVEL_ORDER);

        $external_id = $response['entity']['uuid'];
        $delivery_order_data = $this->getOrderInfo($external_id);

        $delivery_order = new DeliveryOrder();
        $delivery_order['external_id'] = $external_id;
        $delivery_order['order_id'] = $order['id'];
        $delivery_order['delivery_type'] = $this->getTypeObject()->getShortName();
        $delivery_order['number'] = $delivery_order_data['number'];
        $delivery_order['data'] = $delivery_order_data;
        $delivery_order['address'] = $delivery_order->getAddressValue($order);

        if ($delivery_order->insert()) {
            return $delivery_order;
        } else {
            $error_text = t('Ошибка при сохранении заказа на доставку') . ': ' . $delivery_order->getErrorsStr();
            throw new ShopException($error_text, ShopException::ERR_DELIVERY_OTHER_ERROR);
        }
    }

    /**
     * Возвращает "Номер заказа в ИС Клиента" для запроса на создание заказа на доставку
     *
     * @param string $order_num - исходный номер заказа
     * @param int $postfix - постфикс для рекурсивного вызова
     * @return string
     */
    protected function getOrderNumber(string $order_num, int $postfix = 0): string
    {
        $result = $order_num;
        if ($postfix > 0) {
            $result .= "-$postfix";
        }
        if ($this->isTestMode()) {
            $result .= '-' . rand(1000, 9999);
        }

        $exist = (new OrmRequest())
            ->select('number')
            ->from(DeliveryOrder::_getTable())
            ->where(['number' => $result])
            ->exec()->rowCount();

        if ($exist) {
            return $this->getOrderNumber((string)$order_num, ++$postfix);
        } else {
            return $result;
        }
    }

    /**
     * Корректирует заказ в СДЭК
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @param Order $order - заказ
     * @return DeliveryOrder
     * @throws RSException
     * @throws ShopException
     */
    public function changeOrder(DeliveryOrder $delivery_order, Order $order)
    {
        $user = $order->getUser();
        if (!$user->getFio()) {
            throw new ShopException(t('Не указаны Ф.И.О. получателя'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
        }
        if (empty($user['phone'])) {
            throw new ShopException(t('Не указан телефон получателя'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
        }

        $calculation = $this->getPriorityTariff($order);
        if (empty($calculation)) {
            throw new ShopException(t('Нет доступных тарифов'), ShopException::ERR_DELIVERY_RESULT_ERROR);
        }

        //Получаем package_id от старого заказа
        $response = $this->apiRequest(ExternalRequest::METHOD_GET, 'orders', ['im_number' => $delivery_order['number']], LogDeliveryCdek::LEVEL_ORDER);
        $packages = [];
        if (!empty($response['entity']['packages'])) {
            foreach($response['entity']['packages'] as $package) {
                $packages[$package['number']] = $package['package_id'];
            }
        }

        $params = [
            'uuid' => $delivery_order['external_id'],
            'tariff_code' => $calculation['tariff_code'],
            'developer_key' => self::DEVELOPER_KEY,
            /*'delivery_recipient_cost' => [
                'value' => '',
            ],*/
            'recipient' => [
                'name' => $user->getFio(),
                'phones' => [
                    [
                        'number' => $user['phone'],
                    ]
                ],
            ],
            'services' => [],
            'packages' => $this->getOrderPackages($order),
        ];

        foreach($params['packages'] as &$package) {
            if (isset($packages[$package['number']])) {
                $package['package_id'] = $packages[$package['number']];
            }
        }

        $params = array_merge($params, $this->getFromLocationData($calculation));
        $params = array_merge($params, $this->getToLocationData($calculation, $order));

        $this->apiRequest(ExternalRequest::METHOD_PATCH, 'orders', $params, LogDeliveryCdek::LEVEL_ORDER);
        $delivery_order_data = $this->getOrderInfo($delivery_order['external_id']);

        $delivery_order['data'] = $delivery_order_data;
        $delivery_order['address'] = $delivery_order->getAddressValue($order);

        if (!$delivery_order->update()) {
            $error_text = t('Ошибка при сохранении заказа на доставку') . ': ' . $delivery_order->getErrorsStr();
            throw new ShopException($error_text, ShopException::ERR_DELIVERY_OTHER_ERROR);
        }
        return $delivery_order;
    }

    /**
     * Регистрирует отказ по заказу на доставку
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @throws ShopException
     */
    public function refuseOrder(DeliveryOrder $delivery_order): void
    {
        $this->apiRequest(ExternalRequest::METHOD_POST, "orders/{$delivery_order['external_id']}/refusal", [], LogDeliveryCdek::LEVEL_ORDER);
    }

    /**
     * Формирует квитанцию к заказу на доставку
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @return string
     * @throws ShopException
     */
    public function createPrintOrder(DeliveryOrder $delivery_order): string
    {
        $params = [
            'orders' => [
                [
                    'order_uuid' => $delivery_order['external_id'],
                ],
            ],
        ];
        $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'print/orders', $params, LogDeliveryCdek::LEVEL_ORDER);
        if (!$response) {
            throw new ShopException(t('Не удалось выполнить запрос'));
        } elseif (isset($response['error'])) {
            throw new ShopException($response['error']);
        }
        return $response['entity']['uuid'];
    }

    /**
     * Возвращает ссылку на сформированную квитанцию к заказу на доставку
     *
     * @param string $print_uuid
     * @return string
     * @throws ShopException
     */
    public function getPrintOrder(string $print_uuid): string
    {
        $try = 0;
        do {
            time_nanosleep(0, 500000000);
            if (++$try > 10) {
                throw new ShopException(t('Превышено время ожидания документа'));
            }
            $response = $this->apiRequest(ExternalRequest::METHOD_GET, "print/orders/$print_uuid", [], LogDeliveryCdek::LEVEL_ORDER);
            $url = $response['entity']['url'] ?? null;
        } while (!$url);

        return $url;
    }

    /**
     * Формирует квитанцию к заказу на доставку
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @return string
     * @throws ShopException
     */
    public function createPrintBarcode(DeliveryOrder $delivery_order): string
    {
        $type_object = $delivery_order->getOrder()->getDelivery()->getTypeObject();
        $format = $type_object ? $type_object->getOption('barcode_paper_format', 'A4') : 'A4';
        $params = [
            'orders' => [
                [
                    'order_uuid' => $delivery_order['external_id'],
                ],
            ],
            'format' => $format
        ];
        $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'print/barcodes', $params, LogDeliveryCdek::LEVEL_ORDER);
        return $response['entity']['uuid'];
    }

    /**
     * Возвращает ссылку на сформированную квитанцию к заказу на доставку
     *
     * @param string $print_uuid
     * @return string
     * @throws ShopException
     */
    public function getPrintBarcode(string $print_uuid): string
    {
        $try = 0;
        do {
            time_nanosleep(0, 500000000);
            if (++$try > 10) {
                throw new ShopException(t('Превышено время ожидания документа'));
            }
            $response = $this->apiRequest(ExternalRequest::METHOD_GET, "print/barcodes/$print_uuid", [], LogDeliveryCdek::LEVEL_ORDER);
            $url = $response['entity']['url'] ?? null;
        } while (!$url);

        return $url;
    }

    /**
     * Возвращает содержимое документа
     *
     * @param string $url - ссылка на документ
     * @return string
     * @throws ShopException
     */
    public function getDocument(string $url)
    {
        $external_request = (new ExternalRequest(self::EXTERNAL_REQUEST_SOURCE_ID, $url))
            ->setAuthorization([$this, 'getAuthToken'])
            ->setMethod(ExternalRequest::METHOD_GET)
            ->setTimeout($this->getTimeout())
            ->setLog($this->log, LogDeliveryCdek::LEVEL_ORDER)
            ->setEnableCache(false)
            ->setLogOption(ExternalRequest::LOG_OPTION_DONT_WRITE_RESPONSE_BODY, true);

        $external_response = $external_request->executeRequest();

        return $external_response->getRawResponse();
    }

    /**
     * Создаёт заявку на вызов курьера
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @param HttpRequest $request - объект запроса
     * @return void
     * @throws ShopException
     */
    public function createCallCourier(DeliveryOrder $delivery_order, HttpRequest $request): void
    {
        $date = $request->request('date', TYPE_STRING);

        if (!$date) {
            throw new ShopException(t('Укажите дату ожидания курьера'));
        }

        $params = [
            'order_uuid' => $delivery_order['external_id'],
            'intake_date' => date('Y-m-d', strtotime($date)),
            'intake_time_from' => $request->request('time_from', TYPE_STRING),
            'intake_time_to' => $request->request('time_to', TYPE_STRING),
        ];
        if ($lunch_time_from = $request->request('lunch_time_from', TYPE_STRING)) {
            $params['lunch_time_from'] = $lunch_time_from;
        }
        if ($lunch_time_to = $request->request('lunch_time_to', TYPE_STRING)) {
            $params['lunch_time_to'] = $lunch_time_to;
        }
        if ($comment = $request->request('comment', TYPE_STRING)) {
            $params['comment'] = $comment;
        }
        if ($need_call = $request->request('need_call', TYPE_STRING)) {
            $params['need_call'] = true;
        }

        $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'intakes', $params, LogDeliveryCdek::LEVEL_ORDER);
        $call_courier_id = $response['entity']['uuid'];
        $this->apiRequest(ExternalRequest::METHOD_GET, "intakes/$call_courier_id", [], LogDeliveryCdek::LEVEL_ORDER);

        $delivery_order->setExtra(self::DELIVERY_ORDER_EXTRA_KEY_CALL_COURIER_ID, $response['entity']['uuid']);
        $delivery_order->update();
    }

    /**
     * Удаляет заявку на вызов курьера
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @return array
     * @throws ShopException
     */
    public function getCallCourierInfo(DeliveryOrder $delivery_order): array
    {
        $call_courier_id = $delivery_order->getExtra(self::DELIVERY_ORDER_EXTRA_KEY_CALL_COURIER_ID);
        if (!$call_courier_id) {
            throw new ShopException(t('Заявка на вызов курьера не найдена'));
        }
        $response = $this->apiRequest(ExternalRequest::METHOD_GET, "intakes/$call_courier_id", [], LogDeliveryCdek::LEVEL_ORDER);
        return $response['entity'];
    }

    /**
     * Удаляет заявку на вызов курьера
     *
     * @param DeliveryOrder $delivery_order - заказ на доставку
     * @throws ShopException
     */
    public function deleteCallCourier(DeliveryOrder $delivery_order)
    {
        $call_courier_id = $delivery_order->getExtra(self::DELIVERY_ORDER_EXTRA_KEY_CALL_COURIER_ID);
        if (!$call_courier_id) {
            throw new ShopException(t('Заявка на вызов курьера не найдена'));
        }
        try {
            $this->apiRequest(ExternalRequest::METHOD_DELETE, "intakes/$call_courier_id", [], LogDeliveryCdek::LEVEL_ORDER);
            $delivery_order->removeExtra(self::DELIVERY_ORDER_EXTRA_KEY_CALL_COURIER_ID);
            $delivery_order->update();
        } catch (ShopException $e) {
            $error_codes = $e->getExtraData(self::EXCEPTION_ERROR_CODES, []);
            if (in_array('v2_entity_not_found', $error_codes)) {
                $delivery_order->removeExtra(self::DELIVERY_ORDER_EXTRA_KEY_CALL_COURIER_ID);
                $delivery_order->update();
            }
        }
    }

    /**
     * Возвращает данные упаковок заказа
     *
     * @param Order $order
     * @return array
     * @throws RSException
     */
    protected function getOrderPackages(Order $order): array
    {
        $cargos = $order->getCargos();
        if ($cargos) {
            return $this->getOrderPackagesByCargo($order);
        } else {
            return $this->getOrderPackagesDefault($order);
        }
    }

    /**
     * Рассчитывает грузовые места, исходя из указанных пользоателем грузовых мест в заказе
     *
     * @param Order $order
     * @return array
     */
    protected function getOrderPackagesByCargo(Order $order):array
    {
        $payment_type = $order->getPayment()->getTypeObject();
        $delivery_type = $this->getTypeObject();

        $packages = [];
        foreach($order->getCargos() as $cargo) {
            $package = [
                'number' => $order['order_num'].'-'.$cargo['id'],
                'comment' => $cargo['title'],
                'weight' => (int)$cargo->getTotalWeight(),
                'length' => (int)($cargo['dept'] / 10),
                'width' => (int)($cargo['width'] / 10),
                'height' => (int)($cargo['height'] / 10),
                'items' => []
            ];

            foreach($cargo->getCargoItems() as $cargo_item) {
                $order_item = $cargo_item->getOrderItem();
                if ($order_item) {
                    $package['items'][] = [
                        'marking' => $cargo_item->getUit()->asHex(),
                    ] + $this->getOrderPackageItemData(
                            $order,
                            $payment_type,
                            $delivery_type,
                            $order_item,
                            (int)$cargo_item['amount']
                        );
                }
            }
            $packages[] = $package;
        }

        return $packages;
    }

    /**
     * Рассчитывает грузовые места по старому, когда еще не было грузовых мест в ReadyScript.
     * Добавляет все товары в одну коробку и примерно рассчитывает ее возможные габариты
     *
     * @param Order $order
     * @return array
     * @throws RSException
     */
    protected function getOrderPackagesDefault(Order $order): array
    {
        $payment_type = $order->getPayment()->getTypeObject();
        $delivery_type = $this->getTypeObject();

        $package = $this->calculatePackageParams($order->getCart());
        $package['number'] = $order['order_num'];
        $i=0;
        foreach ($order->getCart()->getProductItems() as $n => $item) {
            /** @var OrderItem $product */
            $cart_item = $item[Cart::CART_ITEM_KEY];

            $package['items'][] = [
                'marking' => '',
            ] + $this->getOrderPackageItemData(
                    $order,
                    $payment_type,
                    $delivery_type,
                    $cart_item,
                    (int)$cart_item['amount']
                );


            $i++;
        }
        return [$package];
    }

    /**
     * Формирует сведения об одном товаре, передаваемые в СДЭК
     *
     * @param Order $order
     * @param \Shop\Model\PaymentType\AbstractType  $payment_type
     * @param \Shop\Model\DeliveryType\AbstractType $delivery_type
     * @param OrderItem $order_item
     * @param Float $amount
     * @return array
     */
    protected function getOrderPackageItemData($order, $payment_type, $delivery_type, $order_item, $amount)
    {
        $cash_on_delivery = 0;
        if (!empty($order['payment'])) {
            if ($payment_type->cashOnDelivery()) {
                $cash_on_delivery = 1;
            }
        } else {
            $cash_on_delivery = $delivery_type->getOption('default_cash_on_delivery');
        }
        $decrease_declared_cost = $delivery_type->getOption('decrease_declared_cost');
        $product = $order_item->getEntity();

        $item_title = $order_item['title'];
        $offer_title = $product->getOfferTitle($order_item['offer']);
        if ($offer_title && $offer_title != $item_title) {
            $item_title .= " [$offer_title]";
        }

        $single_cost = ($order_item['price'] - $order_item['discount']) / $order_item['amount'];
        $item_weight = (int)($product->getWeight($order_item['offer'], ProductApi::WEIGHT_UNIT_G) * $amount);
        return [
            'name' => $item_title,
            'ware_key' => $product->getBarCode($order_item['offer']) ?: $product['id'],
            'payment' => [
                'value' => $cash_on_delivery ? $single_cost : 0,
            ],
            'cost' => ($decrease_declared_cost) ? 0 : $single_cost,
            'weight' => $item_weight,
            'amount' => $amount,
            'url' => $product->getUrl(true),
        ];
    }


    /**
     * Возвращает данные места отправки заказа
     *
     * @param array $calculation - данные калькуляции заказа
     * @return array
     * @throws ShopException
     */
    protected function getFromLocationData(array $calculation): array
    {
        $params = [];

        if (in_array($calculation['delivery_mode'], self::DELIVERY_MODES_FROM_PVZ)) {
            if (!$pvz_from = $this->getPvzFrom()) {
                throw new ShopException(t('Не указан ПВЗ отправки'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
            }

            $params['shipment_point'] = $pvz_from->getCode();
        } else {
            if (!$from_location = $this->findLocationIdByAddress($this->getAddressFrom())) {
                throw new ShopException(t('Город отправки не найден в справочнике'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
            }

            $params['from_location'] = [
                'code' => $from_location,
                'address' => $this->getTypeObject()->getOption('address_from', ''),
            ];
        }
        return $params;
    }

    /**
     * Возвращает данные места доставки заказа
     *
     * @param array $calculation - данные калькуляции заказа
     * @param Order $order - заказ
     * @return array
     * @throws ShopException
     */
    protected function getToLocationData(array $calculation, Order $order): array
    {
        $params = [];

        if (in_array($calculation['delivery_mode'], self::DELIVERY_MODES_TO_PVZ)) {
            if (!$pvz_to = $order->getSelectedPvz()) {
                throw new ShopException(t('Не указан ПВЗ доставки'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
            }

            $params['delivery_point'] = $pvz_to->getCode();
        } else {
            if (!$to_location = $this->findLocationIdByAddress($order->getAddress())) {
                throw new ShopException(t('Город доставки не найден в справочнике'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
            }

            $params['to_location'] = [
                'code' => $to_location,
                'address' => $order->getAddress()->getLineView(),
            ];
        }

        return $params;
    }

    /**
     * Возвращает расчёт доставки
     *
     * @param Order $order - заказ
     * @return array
     * @throws RSException
     * @throws ShopException
     */
    public function getPriorityTariff(Order $order): array
    {
        $delivery_data = [];
        foreach ($this->calculateOrder($order) as $item) {
            if (!empty($item['tariff_code'])) {
                $delivery_data[$item['tariff_code']] = $item;
            }
        }

        $selected_tariff = [];
        foreach ($this->getTariffList() as $tariff_id) {
            if (isset($delivery_data[(int)$tariff_id])) {
                $tariff = $delivery_data[$tariff_id];
                if (empty($selected_tariff)) {
                    $selected_tariff = $tariff;
                } else {
                    switch ($this->getTariffPriority()) {
                        case self::TARIFF_PRIORITY_SORT:
                            break 2;
                        case self::TARIFF_PRIORITY_PRICE:
                            if ($tariff['delivery_sum'] < $selected_tariff['delivery_sum']) {
                                $selected_tariff = $tariff;
                            }
                            break;
                        case self::TARIFF_PRIORITY_TIME:
                            if ($tariff['period_max'] < $selected_tariff['period_max']) {
                                $selected_tariff = $tariff;
                            } elseif ($tariff['period_max'] == $selected_tariff['period_max'] && $tariff['delivery_sum'] < $selected_tariff['delivery_sum']) {
                                $selected_tariff = $tariff;
                            }
                            break;
                    }
                }
            }
        }

        if (empty($selected_tariff)) {
            throw new ShopException(t('Нет подходящего тарифа'), ShopException::ERR_DELIVERY_RESULT_ERROR);
        }

        return $selected_tariff;
    }

    /**
     * Возвращает стоимость доставки
     *
     * @param Order $order - объект заказа
     * @param array $services - список дополнительных услуг
     * @return float
     * @throws RSException
     * @throws ShopException
     */
    public function calculateDeliverySum(Order $order, array $services): float
    {
        static $cache = [];

        $this->log->write(t('Начало калькуляции суммы доставки'), LogDeliveryCdek::LEVEL_CALCULATE);

        $calculation = $this->getPriorityTariff($order);
        $tariff_code = $calculation['tariff_code'];

        $services_param = [];
        foreach ($services as $service_code) {
            $services_param[] = ['code' => $service_code];
        }

        try {
            if (!$this->getAddressFrom()) {
                throw new ShopException(t('Не указан город-отправитель'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
            }

            if (!$from_location = $this->findLocationIdByAddress($this->getAddressFrom())) {
                throw new ShopException(t('Город отправки не найден в справочнике'));
            }

            if ($this->getTypeObject()->getOption('show_pvz_from_region_in_moscow')
                && $order['delivery'] == $this->getTypeObject()->getDelivery()->id
                && ($pvz = $order->getSelectedPvz())
                && ($extra = $pvz->getExtra())
                && !empty($extra['city_code']))
            {
                //Выбираем город доставки из ПВЗ
                $to_location = $extra['city_code'];
            } else {
                //Выбираем город доставки из заказа
                $to_location = $this->findLocationIdByAddress($order->getAddress());
            }

            if (!$to_location) {
                throw new ShopException(t('Город доставки не найден в справочнике'));
            }

            $params = [
                'tariff_code' => $tariff_code,
                'from_location' => [
                    'code' => $from_location,
                ],
                'to_location' => [
                    'code' => $to_location,
                ],
                'services' => $services_param,
                'packages' => [$this->calculatePackageParams($order->getCart())],
            ];

            $cache_key = md5(serialize($params));

            if (isset($cache[$cache_key])) {
                $this->log->write(t('Результат калькуляции взят из кэша') . "\n" . var_export($cache[$cache_key], true), LogDeliveryCdek::LEVEL_CALCULATE);
            } else {
                //$params['date'] = date('Y-m-d\TH:i:sO', time() + ($this->getDaysBeforeSend() * 86400));

                $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'calculator/tariff', $params, LogDeliveryCdek::LEVEL_CALCULATE, true);
                $cache[$cache_key] = $response;
            }

            if (isset($cache[$cache_key]['total_sum'])) {
                return $cache[$cache_key]['total_sum'];
            } else {
                throw new ShopException(t('Отсутствует параметр total_sum в ответе СДЭК'));
            }
        } catch (ShopException $e) {
            $this->log->write(t('[Ошибка] ') . $e->getMessage(), LogDeliveryCdek::LEVEL_CALCULATE);
            throw $e;
        }
    }

    /**
     * Возвращает идентификатор валюты СДЭК
     *
     * @return integer
     */
    public function getCdekCurrencyCode(Order $order)
    {
        $cdek_currency = [
            'RUB' => 1,
            'KZT' => 2,
            'USD' => 3,
            'EUR' => 4,
            'GBP' => 5,
            'CNY' => 6,
            'BYR' => 7,
            'UAH' => 8,
            'KGS' => 9,
            'AMD' => 10,
            'TRY' => 11,
            'THB' => 12,
            'KRW' => 13,
            'AED' => 14,
            'UZS' => 15,
            'MNT' => 16,
            'PLN' => 17,
            'AZN' => 18,
            'GEL' => 19,
            'JPY' => 55,
        ];

        return $cdek_currency[$order['currency']] ?? 1;
    }

    /**
     * Возвращает список возможных тарифов доставки заказа
     *
     * @param Order $order - объект заказа
     * @return array
     * @throws ShopException
     * @throws RSException
     */
    public function calculateOrder(Order $order): array
    {
        static $cache = [];

        $this->log->write(t('Начало калькуляции'), LogDeliveryCdek::LEVEL_CALCULATE);

        try {
            if (!$this->getAddressFrom()) {
                throw new ShopException(t('Не указан город-отправитель'), ShopException::ERR_DELIVERY_CHECK_DATA_FAIL);
            }

            if (!$from_location = $this->findLocationIdByAddress($this->getAddressFrom())) {
                throw new ShopException(t('Город отправки не найден в справочнике'));
            }
            if (!$to_location = $this->findLocationIdByAddress($order->getAddress())) {
                throw new ShopException(t('Город доставки не найден в справочнике'));
            }

            $params = [
                'currency' => $this->getCdekCurrencyCode($order),
                'from_location' => [
                    'code' => $from_location,
                ],
                'to_location' => [
                    'code' => $to_location,
                ],
                'packages' => [$this->calculatePackageParams($order->getCart())],
            ];

            $cache_key = md5(serialize($params));

            if (isset($cache[$cache_key])) {
                $this->log->write(t('Результат калькуляции взят из кэша') . "\n" . var_export($cache[$cache_key], true), LogDeliveryCdek::LEVEL_CALCULATE);
            } else {
                //$params['date'] = date('Y-m-d\TH:i:sO', time() + ($this->getDaysBeforeSend() * 86400));

                $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'calculator/tarifflist', $params, LogDeliveryCdek::LEVEL_CALCULATE, true);
                $cache[$cache_key] = $response['tariff_codes'] ?? [];
            }

            return $cache[$cache_key];
        } catch (ShopException $e) {
            $this->log->write(t('[Ошибка] ') . $e->getMessage(), LogDeliveryCdek::LEVEL_CALCULATE);
            throw $e;
        }
    }

    /**
     * Рассчитывает суммарные габариты вес упаковки для указанной корзины товаров
     *
     * @param Cart $cart - корзина заказа
     * @return array
     * @throws RSException
     * @throws ShopException
     */
    protected function calculatePackageParams(Cart $cart)
    {
        $products = $cart->getProductItems();

        $order_weight = 0;
        $i=0;
        $max_sizes = [0, 0, 0];
        $secondary_max_sizes = [0, 0, 0];
        $volume = 0;
        $many_items = count($products) > 1;
        foreach ($products as $n => $item) {
            /** @var OrderItem $product */
            $cart_item = $item[Cart::CART_ITEM_KEY];
            /** @var Product $product */
            $product = $cart_item->getEntity();

            $item_weight = $product->getWeight($cart_item['offer'], ProductApi::WEIGHT_UNIT_G) * $cart_item['amount'];
            $order_weight += $item_weight;

            $dimensions_object = $product->getDimensionsObject();
            $length = $dimensions_object->getLength(ProductDimensions::DIMENSION_UNIT_SM);
            $width = $dimensions_object->getWidth(ProductDimensions::DIMENSION_UNIT_SM);
            $height = $dimensions_object->getHeight(ProductDimensions::DIMENSION_UNIT_SM);
            $sizes = [$length, $width, $height];
            rsort($sizes);
            foreach ($max_sizes as $key => $value) {
                if ($sizes[$key] > $value) {
                    $secondary_max_sizes[$key] = ($item['cartitem']['amount'] > 1) ? $sizes[$key] : $max_sizes[$key];
                    $max_sizes[$key] = $sizes[$key];
                }
            }
            $volume += $dimensions_object->getVolume(ProductDimensions::DIMENSION_UNIT_SM) * $item['cartitem']['amount'];
            if ($item['cartitem']['amount'] > 1) {
                $many_items = true;
            }

            $i++;
        }

        if ($many_items) {
            $max_sizes[2] = $max_sizes[2] + $secondary_max_sizes[2];
            rsort($max_sizes);
            $root3 = pow($volume, 1 / 3);
            if ($max_sizes[0] > $root3) {
                $size_a = $max_sizes[0];
                $volume_left = $volume / $size_a;
                $root2 = sqrt($volume_left);
                if ($max_sizes[1] > $root2) {
                    $size_b = $max_sizes[1];
                    $size_c = $volume_left / $size_b;
                } else {
                    $size_b = $root2;
                    $size_c = $root2;
                }
            } else {
                $size_a = $root3;
                $size_b = $root3;
                $size_c = $root3;
            }
        } else {
            $size_a = ceil($max_sizes[0]);
            $size_b = ceil($max_sizes[1]);
            $size_c = ceil($max_sizes[2]);
        }

        $length = ceil($size_a);
        $width = ceil($size_b);
        $height = ceil($size_c);

        if (!$order_weight) {
            throw new ShopException(t('Упаковка заказа имеет нулевой вес'));
        }
        if (!$length || !$width || !$height) {
            throw new ShopException(t('Упаковка заказа имеет нулевые габариты'));
        }

        $result = [
            'weight' => (int)$order_weight,
            'length' => (int)$length,
            'width' => (int)$width,
            'height' => (int)$height,
        ];
        return $result;
    }

    /**
     * Ищет код населенного пункта в справочнике СДЭК
     *
     * @param Address $address - адрес
     * @return int|false
     */
    protected function findLocationIdByAddress(Address $address)
    {
        static $cache = [];

        $city = $address->getCity();
        $region = $address->getRegion();
        $country = $address->getCountry();
        $this->log->write(t('Поиск СДЭК id для города "%0"', [$city['title']]), LogDeliveryCdek::LEVEL_FIND_CITY);

        $cache_key = ($city['id']) ?: $country['title'] . ',' . $region['title'] . ',' . $city['title'];

        if (isset($cache[$cache_key])) {
            $this->log->write(t('СДЭК id "%0" взят из кэша', [$cache[$cache_key]]), LogDeliveryCdek::LEVEL_FIND_CITY);
        } else {
            if (!empty($city['cdek_city_id'])) {
                $this->log->write(t('СДЭК id "%0" указан принудительно', [$city['cdek_city_id']]), LogDeliveryCdek::LEVEL_FIND_CITY);
                return (int)$city['cdek_city_id'];
            }

            $cdek_region = false;
            $request = (new OrmRequest())
                ->from(new CdekRegion());

            if (!empty($city['fias_guid'])) {
                $this->log->write(t('Поиск по ФИАС "%0"', [$city['fias_guid']]), LogDeliveryCdek::LEVEL_FIND_CITY);
                $cdek_region = (clone $request)
                    ->where(['fias_guid' => $city['fias_guid']])
                    ->object();
            }

            if (!empty($city['kladr_id']) && !$cdek_region) {
                $this->log->write(t('Поиск по КЛАДР "%0"', [$city['kladr_id']]), LogDeliveryCdek::LEVEL_FIND_CITY);
                $cdek_region = (clone $request)
                    ->where(['kladr_code' => $city['kladr_id']])
                    ->object();
            }

            if (!$cdek_region) {
                $this->log->write(t('Поиск по названию "%0, %1, %2"', [$address->getCountry()['title'], $address->getRegion()->getCleanTitle(), $address->getCity()['title']]), LogDeliveryCdek::LEVEL_FIND_CITY);
                $request->where([
                    'city' => $address->getCity()['title'],
                    'country' => $address->getCountry()['title'],
                ]);

                $request->where("region like '%#title%'", [
                    'title' => $address->getRegion()->getCleanTitle(),
                ]);

//                $area = $address->getCity()['area'];
//                if (!empty($area)) {
//                    $request->where(['sub_region' => $area]);
//                }
                $cdek_region = $request->object();
            }

            if ($cdek_region) {
                $this->log->write(t('Найден СДЭК id "%0"', [$cdek_region['code']]), LogDeliveryCdek::LEVEL_FIND_CITY);
                $cache[$cache_key] = (int)$cdek_region['code'];
            } else {
                $this->log->write(t('СДЭК id не найден'), LogDeliveryCdek::LEVEL_FIND_CITY);
                $cache[$cache_key] = false;
            }
        }

        return $cache[$cache_key];
    }

    /**
     * Обновляет базу городов СДЭК пошагово
     *
     * @throws ShopException
     */
    public function updateCdekRegions(): void
    {
        (new OrmRequest())
            ->update(CdekRegion::_getTable())
            ->set(['processed' => 0])
            ->exec();

        $page = 0;
        do {
            $count = $this->updateCdekRegionsPage($page);
            $page++;
        } while ($count);

        (new OrmRequest())
            ->delete()
            ->from(CdekRegion::_getTable())
            ->where(['processed' => 0])
            ->exec();
    }

    /**
     * Обновляет базу городов СДЭК
     *
     * @param int $page - загружаемая страница списка
     * @param string $country Страна
     * @return int|null
     * @throws ShopException
     */
    public function updateCdekRegionsStep(int $page, $country = 'RU'): ?int
    {
        if ($page == 0) {
            (new OrmRequest())
                ->update(CdekRegion::_getTable())
                ->set(['processed' => 0])
                ->where([
                    'country_code' => $country
                ])
                ->exec();
        }

        $count = $this->updateCdekRegionsPage($page, $country);
        if ($count) {
            return ++$page;
        } else {
            (new OrmRequest())
                ->delete()
                ->from(CdekRegion::_getTable())
                ->where([
                    'processed' => 0,
                    'country_code' => $country
                ])
                ->exec();

            return null;
        }
    }

    /**
     * Один шаг обновления базы городов СДЭК, возвращает количество вставленных записей
     *
     * @param int $page - загружаемая страница списка
     * @param string $country
     * @return int
     * @throws ShopException
     */
    public function updateCdekRegionsPage(int $page, $country = 'RU'): int
    {
        $params = [
            'country_codes' => $country,
            'page' => $page,
            'size' => 1000,
        ];
        $region_list = $this->apiRequest(ExternalRequest::METHOD_GET, 'location/cities', $params, LogDeliveryCdek::LEVEL_UPDATE_CDEK_REGIONS);

        if (!empty($region_list)) {
            $values = [];
            foreach ($region_list as $region) {
                if (isset($region['code'])) {
                    $values[] = [
                        'code' => $region['code'],
                        'region_code' => $region['region_code'],
                        'city' => $region['city'] ?? '',
                        'fias_guid' => $region['fias_guid'] ?? '',
                        'kladr_code' => $region['kladr_code'] ?? '',
                        'country' => $region['country'] ?? '',
                        'country_code' => $country,
                        'region' => $region['region'] ?? '',
                        'sub_region' => $region['sub_region'] ?? '',
                        'processed' => 1,
                    ];
                }
            }

            if ($values) {
                (new OrmRequest())
                    ->insert(CdekRegion::_getTable(), ['code', 'region_code', 'city', 'fias_guid', 'kladr_code', 'country', 'country_code', 'region', 'sub_region', 'processed'], ['code', 'fias_guid', 'kladr_code', 'processed'])
                    ->values($values, true)
                    ->exec();
            }
        }

        return count($region_list);
    }

    /**
     * Подписывается на веб-хуки
     *
     * @throws ShopException
     * @return void
     */
    public function webHooksSubscribe(): void
    {
        $params = [
            'url' => RouterManager::obj()->getUrl('shop-front-deliverywebhooks', ['DeliveryType' => $this->getTypeObject()->getShortName()], true),
            'type' => 'ORDER_STATUS',
        ];
        $response = $this->apiRequest(ExternalRequest::METHOD_POST, 'webhooks', $params, LogDeliveryCdek::LEVEL_WEB_HOOK);
        if (!empty($response['entity']['uuid'])) {
            $shop_config = ConfigLoader::byModule('shop');
            $shop_config['cdek_webhook_uuid'] = $response['entity']['uuid'];
            $shop_config->update();
        }
    }

    /**
     * Возвращает информацию о текущей подписке на веб-хуки
     *
     * @return array
     * @throws ShopException
     */
    public function webHooksInfo(): array
    {
        $shop_config = ConfigLoader::byModule('shop');
        $url = "webhooks/{$shop_config['cdek_webhook_uuid']}";

        return $this->apiRequest(ExternalRequest::METHOD_GET, $url, [], LogDeliveryCdek::LEVEL_WEB_HOOK);
    }

    /**
     * Отписывается от получения веб-хуков
     *
     * @param $webhookDisabled - Содержит true, если веб-хук уже отключен, и нужно очистить uuid
     * @return void
     * @throws ShopException
     */
    public function webHooksUnsubscribe($webhookDisabled = false)
    {
        $shop_config = ConfigLoader::byModule('shop');

        if (!$webhookDisabled) {
            $url = "webhooks/{$shop_config['cdek_webhook_uuid']}";
            $this->apiRequest(ExternalRequest::METHOD_DELETE, $url, [], LogDeliveryCdek::LEVEL_WEB_HOOK);
        }

        $shop_config['cdek_webhook_uuid'] = null;
        $shop_config->update();
    }

    /**
     * Запрос к серверу СДЭК
     *
     * @param string $method - метод запроса
     * @param string $script - скрипт
     * @param array $params - массив параметров
     * @param string $log_level - уровень логирования
     * @param bool $use_cache - кэшировать запрос
     * @return mixed
     * @throws ShopException
     */
    public function apiRequest(string $method, string $script, array $params, string $log_level, bool $use_cache = false)
    {
        $source_id = 'delivery_cdek_api';

        $external_request = (new ExternalRequest($source_id, $this->getUrl() . $script))
            ->setAuthorization([$this, 'getAuthToken']) //Будет вызываться только, если в кэше нет данных
            ->setMethod($method)
            ->setContentType(ExternalRequest::CONTENT_TYPE_JSON)
            ->setParams($params)
            ->setTimeout($this->getTimeout())
            ->setLog($this->log, $log_level)
            ->setEnableCache($use_cache)
            ->setExcludeCacheHashParams([
                ExternalRequest::HASH_PARAM_HEADERS //Исключаем авторизационный ключ из кэширования
            ]);

        $external_response = $external_request->executeRequest();

        if (!$external_response->getStatus()) {
            throw new ShopException(t('Сервер СДЭК не доступен'), ShopException::ERR_DELIVERY_API_CONNECT_ERROR);
        }

        $response = $external_response->getResponseJson();

        if (isset($response['requests'])) {
            $request_info = reset($response['requests']);
            if ($request_info['state'] == 'INVALID') {
                $exception_message = t('Ошибка запроса к API СДЭК: ');
                $error_parts = [];
                $error_codes = [];
                foreach ($request_info['errors'] as $error) {
                    $error_parts[] = $error['message'];
                    $error_codes[] = $error['code'];
                }
                $exception_message .= implode(', ', $error_parts);
                throw new ShopException($exception_message, ShopException::ERR_DELIVERY_API_ERROR, null, '', [self::EXCEPTION_ERROR_CODES => $error_codes]);
            }
        }

        return $response;
    }

    /**
     * Возвращает авторизационный токен
     *
     * @return string
     * @throws ShopException
     */
    public function getAuthToken()
    {
        if ($this->auth_token === null) {
            if (empty($this->account) || empty($this->secure_password)) {
                throw new ShopException(t('Не указаны API ключи'));
            }
            $this->auth_token = $this->apiAuthorization($this->account, $this->secure_password);
        }
        return "Bearer ".$this->auth_token;
    }

    /**
     * Делает запрос на авторизацию, возвращает токен
     *
     * @param string $account - account СДЭКа
     * @param string $secure_password - secure_password СДЭКа
     * @return string
     * @throws ShopException
     */
    protected function apiAuthorization(string $account, string $secure_password): string
    {
        $this->log->write(t('Попытка авторизации'), LogDeliveryCdek::LEVEL_AUTHORIZATION);

        if (empty($account) || empty($secure_password)) {
            $this->log->write(t('Не указан идентификатор клиента или секретный ключ'), LogDeliveryCdek::LEVEL_AUTHORIZATION);
            throw new ShopException(t('Не указан идентификатор клиента или секретный ключ'), ShopException::ERR_DELIVERY_API_AUTH_ERROR);
        }

        //Пытаемся получить предыдущий ключ из HashStore
        $auth_hash_store_key = 'cdek-v2-'.$account.'-'.$secure_password;
        $auth_data = HashStoreApi::get($auth_hash_store_key, []);

        if ($auth_data && $auth_data['expire_time'] > time() + 60) {
            $this->log->write(t('Использован предыдущий ключ авторизации'), LogDeliveryCdek::LEVEL_AUTHORIZATION);
            return $auth_data['access_token'];
        }

        //Если там ключ устарел или не существует, то получаем его заново у СДЭК
        $external_response = (new ExternalRequest('delivery_cdek_api', $this->getUrl() . 'oauth/token?parameters'))
            ->setMethod(ExternalRequest::METHOD_POST)
            ->setContentType(ExternalRequest::CONTENT_TYPE_FORM_DATA)
            ->setParams([
                'grant_type' => 'client_credentials',
                'client_id' => $account,
                'client_secret' => $secure_password,
            ])
            ->setTimeout($this->getTimeout())
            ->setLog($this->log, LogDeliveryCdek::LEVEL_AUTHORIZATION)
            ->setEnableCache(false)
            ->executeRequest()
            ->getResponseJson();

        if (!empty($external_response['access_token'])) {
            $this->log->write(t('Авторизация успешна'), LogDeliveryCdek::LEVEL_AUTHORIZATION);

            //Сохраняем ключ в HashStore
            HashStoreApi::set($auth_hash_store_key, $external_response + [
                    'expire_time' => time() + $external_response['expires_in']
                ]);

            return $external_response['access_token'];
        }
        $this->log->write(t('Не удалось получить авторизационный токен'), LogDeliveryCdek::LEVEL_AUTHORIZATION);
        throw new ShopException(t('Не удалось получить авторизационный токен'), ShopException::ERR_DELIVERY_API_AUTH_ERROR);
    }

    /**
     * Возвращает url запросов
     *
     * @return string
     */
    protected function getUrl(): string
    {
        return ($this->isTestMode()) ? self::TEST_URL : self::URL;
    }

    /**
     * Возвращает включён ли тестовый режим
     *
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->test_mode;
    }

    /**
     * Устанавливает тестовый режим
     *
     * @param bool $value
     */
    public function setTestMode(bool $value = true): void
    {
        $this->test_mode = $value;
    }

    /**
     * Возвращает список тарифов
     *
     * @return array
     */
    public function getTariffList(): array
    {
        return $this->tariff_list;
    }

    /**
     * Устанавливает список тарифов
     *
     * @param array $tariff_list - список тарифов
     */
    public function setTariffList(array $tariff_list): void
    {
        $this->tariff_list = $tariff_list;
    }

    /**
     * Возвращает приоритет тарифов
     *
     * @return string
     */
    public function getTariffPriority(): string
    {
        return $this->tariff_priority;
    }

    /**
     * Устанавливает приоритет тарифов
     *
     * @param string $tariff_priority
     */
    public function setTariffPriority(string $tariff_priority): void
    {
        $this->tariff_priority = $tariff_priority;
    }

    /**
     * Возвращает ПВЗ отправки
     *
     * @return Pvz|null
     */
    public function getPvzFrom(): ?Pvz
    {
        return $this->pvz_from;
    }

    /**
     * У
     *
     * @param Pvz $pvz_from - ПВЗ
     */
    public function setPvzFrom(Pvz $pvz_from): void
    {
        $this->pvz_from = $pvz_from;
    }

    /**
     * Возвращает id города-отправителя
     *
     * @return Address
     * @throws ShopException
     */
    public function getAddressFrom(): Address
    {
        if (!$this->address_from) {
            throw (new ShopException(t('Не указан город-отправитель')));
        }
        return $this->address_from;
    }

    /**
     * Устанавливает id города-отправителя
     *
     * @param Address $address_from - адрес
     */
    public function setAddressFrom(Address $address_from): void
    {
        $this->address_from = $address_from;
    }

    /**
     * Возвращает количество дней до планируемой передачи заказа
     *
     * @return int
     */
    public function getDaysBeforeSend(): int
    {
        return $this->days_before_send;
    }

    /**
     * Устанавливает количество дней до планируемой передачи заказа
     *
     * @param int $days_before_send
     */
    public function setDaysBeforeSend(int $days_before_send): void
    {
        $this->days_before_send = $days_before_send;
    }

    /**
     * Возвращает timeout запросов
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Устанавливает timeout запросов
     *
     * @param float $timeout
     */
    public function setTimeout(float $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * Устанавливает данные для авторизации
     *
     * @param string $account - Логин
     * @param string $secure_password - Пароль
     */
    public function setAuthorization(string $account, string $secure_password): void
    {
        $this->account = $account;
        $this->secure_password = $secure_password;
    }

    /**
     * Возвращает объект типа доставки
     *
     * @return Cdek2|null
     */
    public function getTypeObject(): ?Cdek2
    {
        return $this->type_object;
    }

    /**
     * Устанавливает объект типа доставки
     *
     * @param Cdek2 $type_object
     */
    public function setTypeObject(Cdek2 $type_object): void
    {
        $this->type_object = $type_object;

        $this->setAuthorization($type_object->getOption('secret_login', ''), $type_object->getOption('secret_pass', ''));
        $this->setTimeout((float)$type_object->getOption('timeout', CdekApi::DEFAULT_TIMEOUT));
        $this->setDaysBeforeSend((int)$type_object->getOption('day_apply_delivery', 0));
        $this->setTariffList((array)$type_object->getOption('tariffTypeList', 0));
        $this->setTariffPriority((string)$type_object->getOption('tariff_priority', 0));

        $region = new Region($type_object->getOption('city_from'));
        if ($region['id']) {
            $this->setAddressFrom(Address::createFromRegion($region));
        }

        $pvz_json = htmlspecialchars_decode($type_object->getOption('pvz_from', ''));
        $pvz_data = json_decode($pvz_json, true);
        if ($pvz_data) {
            $this->setPvzFrom(Pvz::loadFromArray($pvz_data));
        }

        if ($type_object->getOption('test_mode')) {
            $this->setTestMode();
        }
    }

    /**
     * Возвращает правильный код НДС
     *
     * @param Tax[] $taxes - список налогов
     * @param Address $address - объект адреса
     * @return string|null
     */
    protected function getNdsCode(array $taxes, Address $address)
    {
        $nds = TaxApi::getRightNds($taxes, $address);
        return static::handbookNds()[$nds] ?? null;
    }

    /**
     * Справочник кодов НДС
     * Ключи справочника должны соответствовать списку кодов НДС в TaxApi
     *
     * @return string[]
     */
    protected static function handbookNds()
    {
        static $nds = [
            TaxApi::TAX_NDS_NONE => null,
            TaxApi::TAX_NDS_0 => 0,
            TaxApi::TAX_NDS_10 => 10,
            TaxApi::TAX_NDS_20 => 20,
            TaxApi::TAX_NDS_110 => 10,
            TaxApi::TAX_NDS_120 => 20,
        ];
        return $nds;
    }

    /**
     * Получение стран доступных в СДЕК
     *
     * @return string[]
     */
    public static function staticGetCountries()
    {
        $countries = [];
        $file_path = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/shop' . \Setup::$CONFIG_FOLDER . '/delivery/cdek/cdek_countries.csv';

        $f = fopen($file_path, 'r');
        while ($item = fgetcsv($f, null, ';', '"')) {
            $countries[$item[0]] = $item[1];
        }
        fclose($f);

        return $countries;
    }

    /**
     * Возвращает профиль доставки СДЭК по умолчанию
     *
     * @return \RS\Orm\AbstractObject|Delivery
     */
    public static function getDefaultCdekDelivery()
    {
        $shop_config = ConfigLoader::byModule('shop');

        $delivery = new Delivery($shop_config['cdek_default_delivery']);
        if (!$delivery['id']) {
            $delivery_api = new DeliveryApi();
            foreach ($delivery_api->getList() as $item) {
                /** @var Delivery $item */
                if ($item->getTypeObject() instanceof Cdek2) {
                    $delivery = $item;
                    break;
                }
            }
        }

        if (!$delivery['id']) {
            throw new ShopException(t('Не создано ни одной доставки СДЭК'));
        }
        return $delivery;
    }
}
