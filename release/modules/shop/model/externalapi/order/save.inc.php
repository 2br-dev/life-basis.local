<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;

use Catalog\Model\CurrencyApi;
use Catalog\Model\WarehouseApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Validator\ValidateArray;
use Files\Model\FileApi;
use RS\AccessControl\Rights;
use RS\Application\Application;
use RS\Exception;
use RS\Helper\CustomView;
use RS\Helper\Tools;
use Shop\Config\File;
use Shop\Config\ModuleRights;
use Shop\Model\ActionTemplatesApi;
use Shop\Model\ApiUtils;
use Shop\Model\Cart;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\InterfaceDeliveryOrder;
use Shop\Model\OrderApi;
use Shop\Model\OrderCargoApi;
use Shop\Model\Orm\Order;
use Site\Model\Orm\Site;
use Shop\Model\ExternalApi\Cargo;

/**
 * Метод API - сохраняет заказ и/или возвращает рассчитанные стоимости
 */
class Save extends AbstractAuthorizedMethod
{
    const RIGHT_CALCULATE = 1;
    const RIGHT_SAVE = 2;

    private $validator_order;
    private $validator_order_item;
    private $shop_config;

    function __construct()
    {
        parent::__construct();
        $this->shop_config = File::config();
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
            self::RIGHT_CALCULATE => t('Чтение/расчет заказов'),
            self::RIGHT_SAVE => t('Сохранение заказов')
        ];
    }

    /**
     * Загружает или создает объект заказа
     *
     * @param array $data
     * @return Order
     * @throws ApiException
     */
    protected function loadOrder($data, $refresh_mode)
    {
        if (empty($data['id'])) {
            //Генерируем новый отрицательный временный ID
            $data['id'] = '-1'.Tools::generatePassword(8,range(0,9));
        }

        if ($data['id'] > 0) {
            $order = new Order($data['id']);
            if (!$order['id']) {
                throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
            }
        } else { //У заказа временный отрицательный ID, значит это новый заказ, еще не сохраненный
            $order = new Order();
            $order['id'] = $data['id'];
            $order['_tmpid'] = $data['id'];
            $order['status'] = $this->shop_config['first_order_status'];
            $order['dateof'] = date('Y-m-d H:i:s');
            $order->setCurrency( CurrencyApi::getBaseCurrency() );
        }

        if ($refresh_mode) {
            $order->setRefreshMode(true);
        }

        return $order;
    }

    /**
     * Проверяет права текущего пользователя на доступ к заказу
     *
     * @param Order $order
     * @return void
     */
    protected function validateMethodRights(Order $order)
    {
        if ($order['id'] > 0) {
            $user = $this->token->getUser();

            if ($this->isTokenUserCourier()) {
                if ($order['courier_id'] != $user['id']) {
                    throw new ApiException(t('Курьеры могут работать только с назначенными им заказами'), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
            } elseif ($this->isTokenUserManager()) {
                if ($order['manager_id'] != $user['user_id']) {
                    throw new ApiException(t('Менеджеры могут работать только с назначенными им заказами'), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
            }
        }
    }

    /**
     * Возвращает true, если текущий пользователь менеджер
     *
     * @return bool
     */
    protected function isTokenUserManager()
    {
        return $this->shop_config['manager_group']
            && $this->token->getUser()->inGroup($this->shop_config['manager_group']);
    }

    /**
     * Возвращает true, если текущий пользователь курьер
     *
     * @return bool
     */
    protected function isTokenUserCourier()
    {
        return $this->shop_config['courier_user_group']
            && $this->token->getUser()->inGroup($this->shop_config['courier_user_group']);
    }

    /**
     * Возвращает допустимую структуру значений в переменной data, в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getOrderDataValidator()
    {
        if ($this->validator_order === null) {
            $this->validator_order = new ValidateArray([
                'id' => [
                    '@title' => t('ID заказа'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'status' => [
                    '@title' => t('ID статуса'),
                    '@type' => 'integer',
                ],
                'substatus' => [
                    '@title' => t('ID причины отмены заказа'),
                    '@type' => 'integer',
                ],
                'courier_id' => [
                    '@title' => t('ID курьера'),
                    '@type' => 'integer',
                    '@validate_callback' => function ($value) {
                        $couriers = DeliveryApi::getCourierList();
                        return $value == 0 || isset($couriers[$value]);
                    }
                ],
                'manager_user_id' => [
                    '@title' => t('ID менеджера'),
                    '@type' => 'integer',
                    '@validate_callback' => function ($value) {
                        $managers = OrderApi::getUsersManagers();
                        return $value == 0 || isset($managers[$value]);
                    }
                ],
                'user_id' => [
                    '@title' => t('ID покупателя'),
                    '@type' => 'integer',
                ],
                'user_fio' => [
                    '@title' => t('ФИО покупателя'),
                    '@type' => 'string',
                ],
                'user_email' => [
                    '@title' => t('Email покупателя'),
                    '@type' => 'string',
                ],
                'user_phone' => [
                    '@title' => t('Email покупателя'),
                    '@type' => 'string',
                ],
                'use_addr' => [
                    '@title' => t('ID адреса доставки'),
                    '@type' => 'integer',
                ],
                'delivery' => [
                    '@title' => t('ID способа доставки'),
                    '@type' => 'string',
                ],
                'user_delivery_cost' => [
                    '@title' => t('Произвольная стоимость заказа'),
                    '@type' => 'string',
                ],
                'delivery_extra' => [
                    '@title' => t('Дополнительные параметры доставки (ПВЗ)'),
                    '@type' => 'array',
                ],
                'warehouse' => [
                    '@title' => t('ID склада'),
                    '@type' => 'integer',
                    '@validate_callback' => function ($value) {
                        $warehouse_api = new WarehouseApi();
                        return $value == 0 || $warehouse_api->getOneItem($value) !== false;
                    }
                ],
                'payment' => [
                    '@title' => t('ID способа оплаты'),
                    '@type' => 'string',
                ],
                'is_payed' => [
                    '@title' => t('Флаг оплаты'),
                    '@type' => 'integer',
                    '@allowable_values' => [1, 0]
                ],
                'admin_comments' => [
                    '@title' => t('Комментарий администратора'),
                    '@type' => 'string'
                ],
                'user_text' => [
                    '@title' => t('Текст для покупателя'),
                    '@type' => 'string'
                ],
                'userfields_arr' => [
                    '@title' => t('Массив значений дополнительных полей. В ключе - alias доп.поля, в значении - значение поля'),
                    '@type' => 'array'
                ],
                'notify_user' => [
                    '@title' => t('Уведомить пользователя об изменениях'),
                    '@type' => 'integer',
                    '@allowable_values' => [1, 0]
                ],
                'items' => [
                    '@title' => t('Массив элементов (товаров, скидок, и т.д.) заказа'),
                    '@type' => 'array',
                    '@arrayitemtype' => 'array'
                ],
                'trigger_cart_change' => [
                    '@title' => t('Пересчитать скидки'),
                    '@type' => 'integer',
                    '@allowable_values' => [1, 0]
                ],
                'create_refund_receipt' => [
                    '@title' => t('Выбить чек возврата'),
                    '@type' => 'integer',
                    '@allowable_values' => [1, 0]
                ],
                'shipment_date' => [
                    '@title' => t('Дата отгрузки'),
                    '@type' => 'string',
                ],
                'files' => [
                    '@title' => t('Список прикрепленных файлов'),
                    '@type' => 'array',
                ],
                'contact_person' => [
                    '@title' => t('Контактное лицо'),
                    '@type' => 'string',
                ],
                'track_number' => [
                    '@title' => t('Трек-номер'),
                    '@type' => 'string',
                ],
                'saved_payment_method_id' => [
                    '@title' => t('Выбранный "сохранённый метод оплаты"'),
                    '@type' => 'integer',
                ],
            ]);
        }

        $this->validator_order->setDontCheckExtraKeys(true);
        return $this->validator_order;
    }

    /**
     * Возвращает допустимую структуру значений в переменной data, в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getOrderItemValidator()
    {
        if ($this->validator_order_item === null) {
            $this->validator_order_item = new ValidateArray([
                'uniq' => [
                    '@title' => t('Уникальный идентификатор позиции заказа, 10 символов'),
                    '@type' => 'string',
                    '@require' => true
                ],
                'title' => [
                    '@title' => t('Название позиции'),
                    '@type' => 'string',
                    '@require' => true
                ],
                'entity_id' => [
                    '@title' => t('ID связанного объекта'),
                    '@type' => 'string',
                ],
                'type' => [
                    '@title' => t('Тип связанного объекта'),
                    '@type' => 'string',
                    '@require' => true
                ],
                'single_weight' => [
                    '@title' => t('Вес одной позиции'),
                    '@type' => 'float',
                ],
                'single_cost' => [
                    '@title' => t('Цена одной позиции'),
                    '@type' => 'float',
                ],
                'cost_id' => [
                    '@title' => t('Тип цены'),
                    '@type' => 'integer',
                ],
                'code' => [
                    '@title' => t('Купон'),
                    '@type' => 'string',
                ],
                'amount' => [
                    '@title' => t('Количество'),
                    '@type' => 'float',
                ],
                'price' => [
                    '@title' => t('Общая стоимость позиции'),
                    '@type' => 'float',
                ],
                'discount' => [
                    '@title' => t('Общая скидка позиции'),
                    '@type' => 'float',
                ],
                'discount_from_old_cost' => [
                    '@title' => t('Скидка от зачеркнутой цены'),
                    '@type' => 'float',
                ],
                'multioffers_values' => [
                    '@title' => t('Значения многомерных комплектаций (в ключе ID хар-ки, в значении - текст)'),
                    '@type' => 'array',
                ],
                'offer' => [
                    '@title' => t('ID комплектации'),
                    '@type' => 'integer',
                ],
            ]);
        }

        $this->validator_order_item->setDontCheckExtraKeys(true);
        return $this->validator_order_item;
    }

    /**
     * Форматирует комментарий, полученный из PHPDoc
     *
     * @param string $text - комментарий
     * @return string
     */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);

        $validator_order = $this->getOrderDataValidator();
        $validator_order_item = $this->getOrderItemValidator();

        $text = preg_replace_callback('/\#data-order-info/', function() use($validator_order) {
            return $validator_order->getParamInfoHtml();
        }, $text);

        $text = preg_replace_callback('/\#data-order-item-info/', function() use($validator_order_item) {
            return $validator_order_item->getParamInfoHtml();
        }, $text);

        return $text;
    }

    /**
     * Проверяет входящие данные
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    private function validateData(array $data, Order $order)
    {
        $this->getOrderDataValidator()->validate('data', $data, $this->method_params);

        if (!empty($data['items'])) {
            foreach($data['items'] as $n => $item) {
                $this->getOrderItemValidator()->validate('data.items.'.$n, $item, $this->method_params);
            }
        }
    }

    /**
     * Заполняет заказ данными
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    protected function fillData($data, $order)
    {
        if ($order['id'] > 0) {
            if (isset($data['warehouse']) && $data['warehouse'] != $order['warehouse']) {
                $order['back_warehouse'] = $order['warehouse']; //Запишем склад на который надо вернуть остатки
            }

            if ($order['use_addr']) {
                $order['before_address'] = $order->getAddress();
            }
        }

        if (isset($data['delivery_extra'])) {
            $order->addExtraKeyPair('delivery_extra', $data['delivery_extra']);
            unset($data['delivery_extra']);
        }

        //Временный патч
        if (($data['notify_user'] ?? '') == 'true') {
            $data['notify_user'] = 1;
        }

        //Оставляем для заказа только ключи, присутствующие в схеме валидации
        $data = array_intersect_key($data, array_flip(array_keys($this->getOrderDataValidator()->getSchema())));
        unset($data['id']);
        $order->getFromArray($data);

        if (isset($data['items'])) {
            if (Rights::hasRight($this, ModuleRights::RIGHT_PRODUCTS_CHANGING)
                || Rights::hasRight($this, ModuleRights::RIGHT_PRODUCTS_DELETE))
            {
                $allowed_keys = array_flip(array_keys($this->getOrderItemValidator()->getSchema()))
                    + ['multioffers' => true];

                $items = [];
                foreach($data['items'] as $item) {
                    $item['multioffers'] = $item['multioffers_values'] ?? [];
                    $items[$item['uniq']] = array_intersect_key($item, $allowed_keys);
                }

                $order->getCart()->updateOrderItems($items);
            }
            unset($data['items']);
        }
    }

    /**
     * Добавляет данные, которые могут быть исключительно администраторам
     *
     * @param Order $order
     * @param $response
     * @return array
     */
    protected function addAdminData(Order $order, $response)
    {
        //Добавляем все файлы в список
        $response['order']['files'] = $this->getFilesSection($order);
        $response['order']['canFastResponse'] = $this->canFastResponse();
        $response['order']['shipments'] = $this->getShipmentsSection($order);
        $response['order']['print_documents'] = $this->getPrintDocsSection($order);
        $response['order']['cargos'] = $this->getCargosSection($order);
        $response['order']['delivery_orders'] = $this->getDeliveryOrderSection($order);

        return $response;
    }

    /**
     * Возвращает список добавленных грузовых мест
     *
     * @param Order $order
     * @return array
     */
    protected function getCargosSection($order)
    {
        $order_cargo_api = new OrderCargoApi();
        $order_cargo_api->setFilter('order_id', $order['id']);

        return Cargo\GetList::prepareCargosForApi($order_cargo_api->getList());
    }

    /**
     * Возвращает список проведенных отгрузок
     *
     * @param Order $order
     * @return array
     */
    protected function getShipmentsSection($order)
    {
        $result = [];
        $shipments = $order->getShipments();
        $base_currency = CurrencyApi::getBaseCurrency()->stitle;

        foreach($shipments as $shipment) {
            $data = $shipment->getValues();
            $items = $shipment->getShipmentItems();
            $data['info_total_sum_formatted'] = CustomView::cost($data['info_total_sum'], $base_currency);
            $data['items_count'] = count($items);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Возвращает подготовленную секцию files с файлами со всеми правами
     *
     * @param Order $order
     * @return array
     */
    protected function getFilesSection($order)
    {
        $file_api = new FileApi();
        $file_api->setFilter([
            'link_type_class' => 'files-shoporder',
            'link_id' => $order['id']
        ]);
        $order_files = [];
        foreach($file_api->getList() as $file) {
            try {
                $access_text = $file->getLinkType()->getAccessTypes()[$file['access']] ?? '';
                $access_text = is_array($access_text) ? $access_text['title'] : $access_text;
            } catch (Exception $e) {
                $access_text = '';
            }

            $order_files[] = [
                'uniq' => $file['uniq'],
                'title' => $file['name'],
                'size' => $file['size'],
                'description' => $file['description'],
                'link'  => $file->getUrl(true),
                'access' => $file['access'],
                'access_text' => $access_text,
            ];
        }

        return $order_files;
    }

    /**
     * Возвращает true, если в админ.панели существуют сообщения для быстрого ответа
     *
     * @return bool
     */
    protected function canFastResponse()
    {
        $action_template_api = new ActionTemplatesApi();
        $action_template_api->setFilter('public', 1);
        return $action_template_api->getListCount() > 0;
    }

    /**
     * Возвращает сведения для печати документов по заказу
     *
     * @param Order $order
     * @return array
     */
    private function getPrintDocsSection(Order $order)
    {
        $result = [];
        if ($order['id'] > 0) {

            foreach($order->getPrintForms() as $id => $print_form) {
                $print_form->setOrder($order);

                $result[] = [
                    'title' => $print_form->getTitle(),
                    'type' => $id,
                    'url' => $print_form->getPublicUrl()
                ];
            }
        }

        return $result;
    }


    /**
     * Возвращает секцию с информацией о заказах на доставку
     *
     * @param Order $order
     * @param $response
     * @return array
     */
    protected function getDeliveryOrderSection(Order $order)
    {
        $result = [];
        $delivery = $order->getDelivery();
        $delivery_type = $delivery->getTypeObject();
        if ($delivery_type instanceof InterfaceDeliveryOrder) {
            foreach($delivery_type->getDeliveryOrderList($order) as $delivery_order) {
                $result[] = \Shop\Model\ExternalApi\DeliveryOrder\Get::getDeliveryOrderData($delivery_order, $delivery_type, false);
            }
        }

        return $result;
    }

    /**
     * Рассчитывает, создает или обновляет заказ
     * ---
     * Перед редактированием нового заказа, вызовите метод с параметром data.id:0, refresh_mode:1. В ответ будет возвращен
     * шаблон нового заказа с присвоенным временным отрицательным ID, который затем можно дополнять данными.
     * Последующие запросы для нового заказа нужно отправлять уже с тем отрицательным ID, который был получен.
     *
     * Перед редактированием существующего заказа, вызовите метод с параметром data.id:<i>ID существующего заказа</i>, refresh_mode:1.
     * В ответ будет возвращен JSON со сведениями о существующем заказе, в котором можно изменять данные.
     *
     * JSON, возвращаемый в секции order в ответе можно в том же полном виде отправлять в параметре data в запросе order.save.
     * У данного метода отключена проверка на наличие лишних полей, но обрабатываться будут только описанные в
     * документации поля из параметра data.
     *
     * Для сохранения изменений, используйте refresh_mode:0.
     *
     * @param string $token Авторизационный токен
     * @param array $data Полные сведения о заказе
     * #data-order-info
     *
     * Структура данных поля <b>data.items</b>:
     * #data-order-item-info
     * @param int $refresh_mode Если 1, то происходит расчет стоимости заказа без его сохранения. Если 0, то заказ будет сохранен.
     *
     * @return array Возвращает полный объект заказа
     *
     * @example POST api/methods/order.save?token=311211047ab5474dd67ef88345313a6e479bf616
     * JSON body:
     * <pre>  {
     *     "data":{
     *       "id": 1537,
     *     },
     *     "refresh_mode": 1
     *   }
     * </pre>
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true,
                "order": {
                    "id": 1537,
                    "order_num": "484836",
                    "user_id": "2",
                    "currency": "RUB",
                    "currency_ratio": "1",
                    "currency_stitle": "₽",
                    "ip": "127.0.0.1",
                    "manager_user_id": "2",
                    "dateof": "2024-08-01 18:32:22",
                    "dateofupdate": "2024-09-14 14:57:51",
                    "shipment_date": "0000-00-00 00:00:00",
                    "creator_platform_id": "site",
                    "totalcost": 16310,
                    "profit": "3262.00",
                    "user_delivery_cost": "0.00",
                    "is_payed": "0",
                    "status": "2",
                    "admin_comments": "fjsgfjksdfhsf",
                    "user_text": "<p><strong>Привет</strong>!</p>",
                    "hash": "9b462a4b70975697f01a6e3f85410ae9",
                    "track_number": "",
                    "saved_payment_method_id": null,
                    "trigger_cart_change": null,
                    "contact_person": "",
                    "use_addr": "321",
                    "only_pickup_points": null,
                    "userfields_arr": {
                        "string": "123235",
                        "checkbox": "1",
                        "list": "первый",
                        "textarea": "hello world"
                    },
                    "delivery": "1",
                    "courier_id": "0",
                    "warehouse": "3",
                    "payment": "2",
                    "comments": "test",
                    "user_fio": null,
                    "user_email": null,
                    "user_phone": null,
                    "is_mobile_checkout": null,
                    "regfields": null,
                    "true_weight": "0",
                    "review_is_send": "0",
                    "rate": "0",
                    "partner_id": null,
                    "dateof_iso": "2024-08-01T18:32:22+03:00",
                    "dateof_timestamp": 1722526342,
                    "dateof_date": "01.08.2024",
                    "total_discount_unformatted": null,
                    "total_discount": null,
                    "dateof_datetime": "01.08.2024 18:32",
                    "items": [
                        {
                            "uniq": "3",
                            "type": "tax",
                            "entity_id": "7",
                            "offer": null,
                            "multioffers": [],
                            "amount": 1,
                            "title": "НДС, 20%(включен в стоимость)",
                            "extra": null,
                            "extra_arr": [],
                            "order_id": "1537",
                            "barcode": null,
                            "sku": null,
                            "model": null,
                            "single_weight": null,
                            "single_cost": null,
                            "price": "2685.00",
                            "profit": "0.00",
                            "discount": "2685.00",
                            "unit_id": null,
                            "sortn": "5",
                            "image": null,
                            "url": null,
                            "cost": 0,
                            "cost_formatted": "0 ₽",
                            "price_formatted": "2 685 ₽",
                            "discount_formatted": "2 685 ₽",
                            "unit_title": "",
                            "offer_type": "single"
                        },
                        {
                            "uniq": "4",
                            "type": "delivery",
                            "entity_id": "1",
                            "offer": null,
                            "multioffers": [],
                            "amount": 1,
                            "title": "Доставка: Самовывоз",
                            "extra": null,
                            "extra_arr": [],
                            "order_id": "1537",
                            "barcode": null,
                            "sku": null,
                            "model": null,
                            "single_weight": null,
                            "single_cost": null,
                            "price": "0.00",
                            "profit": "0.00",
                            "discount": "0.00",
                            "unit_id": null,
                            "sortn": "6",
                            "image": null,
                            "url": null,
                            "cost": 0,
                            "cost_formatted": "0 ₽",
                            "price_formatted": "0 ₽",
                            "discount_formatted": "0 ₽",
                            "unit_title": "",
                            "offer_type": "single"
                        },
                        {
                            "uniq": "icwn6s7bm3",
                            "type": "product",
                            "entity_id": "76570",
                            "offer": "7213",
                            "multioffers": [],
                            "amount": 1,
                            "title": "Моноблок MSI Wind Top AP1920 Black",
                            "extra": null,
                            "extra_arr": null,
                            "order_id": null,
                            "barcode": null,
                            "sku": null,
                            "model": null,
                            "single_weight": "100",
                            "single_cost": "16310.00",
                            "price": "16310.00",
                            "profit": null,
                            "discount": "200.00",
                            "unit_id": null,
                            "sortn": null,
                            "image": {
                                "id": "2808",
                                "title": "",
                                "original_url": "https://full.readyscript.local/storage/photo/original/e/j4rnjiigtk2dnss.jpg",
                                "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/e/j4rnjiigtk2dnss_daa12bb1.jpg",
                                "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/e/j4rnjiigtk2dnss_e58508d.jpg",
                                "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/e/j4rnjiigtk2dnss_6781b64f.jpg",
                                "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/e/j4rnjiigtk2dnss_8691dcb1.jpg",
                                "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/e/j4rnjiigtk2dnss_134aadfb.jpg"
                            },
                            "url": "https://full.readyscript.local/product/monoblok-msi-wind-top-ap1920-black-1/",
                            "cost": 16110,
                            "cost_formatted": "16 110 ₽",
                            "price_formatted": "16 310 ₽",
                            "discount_formatted": "200 ₽",
                            "unit_title": "",
                            "offer_type": "single"
                        },
                        {
                            "uniq": "2",
                            "type": "subtotal",
                            "entity_id": null,
                            "offer": null,
                            "multioffers": [],
                            "amount": 1,
                            "title": "Товаров на сумму",
                            "extra": null,
                            "extra_arr": null,
                            "order_id": null,
                            "barcode": null,
                            "sku": null,
                            "model": null,
                            "single_weight": null,
                            "single_cost": null,
                            "price": "16110.00",
                            "profit": null,
                            "discount": "16110.00",
                            "unit_id": null,
                            "sortn": null,
                            "image": null,
                            "url": null,
                            "cost": 0,
                            "cost_formatted": "0 ₽",
                            "price_formatted": "16 110 ₽",
                            "discount_formatted": "16 110 ₽",
                            "unit_title": "",
                            "offer_type": "single"
                        }
                    ],
                    "track_url": null,
                    "files": [
                        {
                            "id": "323",
                            "title": "rs-loader.gif",
                            "link": "https://full.readyscript.local/download-file/rs-loader-3.gif",
                            "access": "visible",
                            "access_text": "публичный"
                        }
                    ],
                    "additional_fields": [
                        {
                            "title": "Строка",
                            "value": "123235",
                            "alias": "string",
                            "type": "string"
                        },
                        {
                            "title": "Чекбокс",
                            "value": "Да",
                            "alias": "checkbox",
                            "type": "bool"
                        },
                        {
                            "title": "Список",
                            "value": "первый",
                            "alias": "list",
                            "type": "list",
                            "values": [
                                "первый",
                                "второй"
                            ]
                        },
                        {
                            "title": "Текстареа",
                            "value": "hello world",
                            "alias": "textarea",
                            "type": "text"
                        }
                    ],
                    "totalcost_formatted": "16 310 ₽",
                    "can_online_pay": false,
                    "can_edit": true,
                    "creator_platform_title": "Сайт",
                    "extra_info": {
                        "affiliate": {
                            "title": "Выбранный город при оформлении",
                            "value": "Краснодар",
                            "data": {
                                "id": "109"
                            },
                            "type": "default"
                        }
                    },
                    "delivery_extra": {
                        "pvz_data": "{&quot;code&quot;:&quot;1&quot;,&quot;title&quot;:&quot;ТРЦ Красная Площадь&quot;,&quot;country&quot;:&quot;&quot;,&quot;region&quot;:&quot;&quot;,&quot;city&quot;:&quot;&quot;,&quot;address&quot;:&quot;Москва&quot;,&quot;phone&quot;:&quot;&quot;,&quot;worktime&quot;:&quot;&quot;,&quot;coord_x&quot;:37.6188,&quot;coord_y&quot;:55.7802,&quot;note&quot;:&quot;&quot;,&quot;cost&quot;:0,&quot;payment_by_cards&quot;:false,&quot;preset&quot;:&quot;islands#redIcon&quot;,&quot;extra&quot;:[]}"
                    }
                },
                "user": {
                    "2": {
                        "id": "2",
                        "name": "Артем",
                        "surname": "Иванов",
                        "midname": "Петрович",
                        "e_mail": "demo@example.com",
                        "login": "demo@example.com",
                        "phone": "+79280000001",
                        "sex": "",
                        "subscribe_on": "0",
                        "is_enable_two_factor": "0",
                        "creator_app_id": "",
                        "is_company": "0",
                        "company": "ООО Ромашка",
                        "company_inn": "1234567890",
                        "data": {
                            "test2": "123"
                        },
                        "user_cost": {
                            "1": "0",
                            "4": "0"
                        },
                        "push_lock": null,
                        "passport": "серия 03 06, номер 123456, выдан УВД Западного округа г. Краснодар, 04.03.2006",
                        "company_kpp": "0987654321",
                        "company_ogrn": "1234567890",
                        "company_v_lice": "директора Сидорова Семена Петровича",
                        "company_deistvuet": "устава",
                        "company_bank": "ОАО УРАЛБАНК",
                        "company_bank_bik": "1234567890",
                        "company_bank_ks": "10293847560192837465",
                        "company_rs": "19283746510293847560",
                        "company_address": "350089, г. Краснодар, ул. Чекистов, 12",
                        "company_post_address": "350089, г. Краснодар, ул. Чекистов, 15",
                        "company_director_post": "директор",
                        "company_director_fio": "Сидоров С.П.",
                        "manager_user_id": "0",
                        "basket_min_limit": "0.00"
                    }
                },
                "status": {
                    "2": {
                        "id": "2",
                        "title": "Ожидает оплату",
                        "parent_id": "0",
                        "bgcolor": "#687482",
                        "type": "waitforpay"
                    }
                },
                "address": {
                    "321": {
                        "id": "321",
                        "user_id": "2",
                        "order_id": "0",
                        "zipcode": "350062",
                        "country": "Россия",
                        "region": "Краснодарский край",
                        "city": "Краснодар",
                        "address": "",
                        "street": "",
                        "house": "",
                        "block": "",
                        "apartment": "",
                        "entrance": "",
                        "entryphone": "",
                        "floor": "",
                        "subway": "",
                        "city_id": "3375",
                        "region_id": "3374",
                        "country_id": "1",
                        "deleted": "0",
                        "extra": [],
                        "_extra": "[]",
                        "coords": "",
                        "line_view_short": "",
                        "line_view_full": "350062, Россия, Краснодарский край, Краснодар"
                    }
                },
                "warehouse": {
                    "3": {
                        "id": "3",
                        "title": "ТРЦ Галерея",
                        "alias": "vtoroy-sklad",
                        "group_id": "1",
                        "image": null,
                        "description": "<p>Отличный второй склад</p>",
                        "adress": "улица Красных Партизан, 238",
                        "phone": "89282087908",
                        "work_time": "c 10 до 18",
                        "coor_x": "45.0515",
                        "coor_y": "38.9552",
                        "default_house": "0",
                        "public": "1",
                        "checkout_public": "1",
                        "dont_change_stocks": "0",
                        "use_in_sitemap": "0",
                        "xml_id": "103",
                        "is_payment_by_card": "0",
                        "meta_title": "",
                        "meta_keywords": "",
                        "meta_description": "",
                        "schedule_items": [],
                        "affiliate_id": "109",
                        "sber_id": "0",
                        "fp_help": null,
                        "fp_country_id": "RU",
                        "fp_region_code": "77",
                        "fp_federal_district": "",
                        "fp_region": "",
                        "fp_city": "",
                        "fp_street": "",
                        "fp_house_number": "",
                        "fp_index": "",
                        "fp_time_zone": "Europe/Moscow",
                        "fp_external_id": "",
                        "linked_region_id": "3375"
                    }
                },
                "delivery": {
                    "1": {
                        "id": "1",
                        "title": "Самовывоз",
                        "admin_suffix": "",
                        "description": "Пункты выдачи товаров см. в разделе контакты",
                        "picture": null,
                        "parent_id": "0",
                        "xzone": null,
                        "free_price": "0",
                        "first_status": "2",
                        "user_type": "all",
                        "extrachange_discount": "0",
                        "extrachange_discount_implementation": "1",
                        "public": "1",
                        "default": "0",
                        "payment_method": "0",
                        "class": "myself",
                        "min_price": null,
                        "max_price": null,
                        "min_weight": null,
                        "max_weight": null,
                        "min_cnt": "0",
                        "delivery_periods": [],
                        "tax_ids": [
                            "2"
                        ],
                        "mobilesiteapp_additional_html": null,
                        "mobilesiteapp_description": "",
                        "show_on_partners": [],
                        "has_pvz": true
                    }
                },
                "payment": {
                    "2": {
                        "id": "2",
                        "title": "Квитанция банка",
                        "admin_suffix": "",
                        "description": "Система предложит распечатать бланк, для оплаты в любом отделении банка.",
                        "picture": null,
                        "first_status": null,
                        "user_type": "all",
                        "target": "all",
                        "delivery": [],
                        "public": "1",
                        "default_payment": "0",
                        "commission": "0",
                        "commission_include_delivery": "0",
                        "commission_as_product_discount": "1",
                        "class": "formpd4",
                        "min_price": null,
                        "max_price": null,
                        "show_on_warehouses": [],
                        "success_status": "4",
                        "holding_status": "0",
                        "holding_cancel_status": "0",
                        "create_cash_receipt": "1",
                        "payment_method": "0",
                        "create_order_transaction": "1",
                        "affiliate_id_arr": null,
                        "show_on_partners": [],
                        "telegram_provider_token": null,
                        "hide_in_telegram": "0",
                        "docs": [
                            {
                                "title": "Квитанция",
                                "link": "https://full.readyscript.local/paydocuments/?doc_key=pd4&order=9b462a4b70975697f01a6e3f85410ae9"
                            }
                        ]
                    }
                },
                "view_scheme": {
                    "status": {
                        "editable": true
                    },
                    "blocks": [
                        {
                            "title": "Покупатель",
                            "type": "block-standard",
                            "fields": [
                                {
                                    "title": "Покупатель",
                                    "key": "user_id",
                                    "type": "client",
                                    "editable": true
                                },
                                {
                                    "title": "E-mail",
                                    "key": "user_email",
                                    "type": "client-email"
                                },
                                {
                                    "title": "Телефон",
                                    "key": "user_phone",
                                    "type": "client-phone"
                                },
                                {
                                    "title": "Дополнительные сведения",
                                    "type": "client-extra-data"
                                }
                            ]
                        },
                        {
                            "title": "Информация",
                            "type": "block-standard",
                            "fields": [
                                {
                                    "title": "Комментарий администратора",
                                    "key": "admin_comments",
                                    "type": "textarea",
                                    "editable": true
                                },
                                {
                                    "title": "Комментарий клиента",
                                    "key": "comment",
                                    "type": "textarea"
                                },
                                {
                                    "title": "Последнее обновление",
                                    "key": "dateofupdate",
                                    "type": "datetime",
                                    "showIfExists": true
                                },
                                {
                                    "title": "Дата отгрузки",
                                    "key": "shipment_date",
                                    "type": "datetime",
                                    "editable": true
                                },
                                {
                                    "title": "Платформа, на которой создан заказ",
                                    "key": "creatorPlatformTitle",
                                    "type": "string"
                                },
                                {
                                    "title": "Менеджер заказа",
                                    "key": "managerUserId",
                                    "editable": true,
                                    "type": "user",
                                    "data": {
                                        "filter": {
                                            "is_courier": 1
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            "title": "Доставка",
                            "type": "block-standard",
                            "fields": [
                                {
                                    "title": "Адрес",
                                    "type": "address"
                                },
                                {
                                    "title": "Способ доставки",
                                    "type": "delivery",
                                    "editable": true
                                },
                                {
                                    "title": "Пункт выдачи",
                                    "type": "pvz"
                                },
                                {
                                    "title": "Курьер",
                                    "type": "user",
                                    "key": "courierId",
                                    "editable": true,
                                    "data": {
                                        "filter": {
                                            "is_courier": 1
                                        }
                                    }
                                },
                                {
                                    "title": "Контактное лицо",
                                    "key": "contact_person",
                                    "type": "string",
                                    "editable": true
                                },
                                {
                                    "title": "Склад",
                                    "key": "warehouse",
                                    "type": "warehouse",
                                    "editable": true
                                }
                            ]
                        },
                        {
                            "title": "Оплата",
                            "type": "block-standard",
                            "fields": [
                                {
                                    "title": "Способ оплаты",
                                    "key": "payment",
                                    "type": "payment",
                                    "editable": true
                                },
                                {
                                    "title": "Заказ оплачен?",
                                    "key": "is_payed",
                                    "type": "boolean",
                                    "editable": true
                                },
                                {
                                    "title": "Документы покупателя",
                                    "type": "payment-documents"
                                },
                                {
                                    "title": "Транзакции",
                                    "type": "payment-transactions"
                                }
                            ]
                        },
                        {
                            "title": "Дополнительная информация",
                            "type": "block-standard",
                            "fields": [
                                {
                                    "title": "Доход от заказа",
                                    "key": "profit",
                                    "type": "money"
                                },
                                {
                                    "title": "Дополнительная информация",
                                    "type": "extra-info"
                                }
                            ]
                        },
                        {
                            "title": "Текст для покупателя",
                            "hint": "Будет виден покупателю на странице просмотра заказа",
                            "type": "block-standard",
                            "fields": [
                                {
                                    "title": "Текст для покупателя",
                                    "placeholder": "Напишите любой текст, который должен увидеть пользователь",
                                    "key": "user_text",
                                    "type": "richtext",
                                    "editable": true
                                }
                            ]
                        },
                        {
                            "title": "Прикрепленные файлы",
                            "hint": "Будут видны покупателю на странице просмотра заказа",
                            "type": "block-wide",
                            "fields": [
                                {
                                    "type": "files",
                                    "editable": true
                                }
                            ]
                        }
                    ]
                },
                "site_uid": "91d8b18c4cc70da5e0d4a0ab8eb6186bbd1418d3"
            }
        }
     * </pre>
     */
    public function process($token, $data, $refresh_mode)
    {
        $order = $this->loadOrder($data, $refresh_mode);

        $this->validateMethodRights($order);
        $this->validateData($data, $order);
        $this->fillData($data, $order);

        if (!$refresh_mode) {
            if ($order['id'] < 0) {
                $result = $order->insert();
            } else {
                $result = $order->update();
            }

            if ($result) {
                $order->getCart()->saveOrderData();
            } else {
                throw new ApiException($order->getErrorsStr(), ApiException::ERROR_WRITE_ERROR);
            }
        }

        $site = new Site($order['__site_id']->get());
        $order = ApiUtils::addOrderItems($order);
        $response = ApiUtils::getFullOrderResponse($order);
        $response = $this->addAdminData($order, $response);
        $response = $this->addItemsCargoInfo($order, $response);

        return [
            'response' => [
                'success' => true,
                ...$response,
                'view_scheme' => ApiUtils::getOrderViewScheme($order),
                'site_uid' => $site->getSiteHash()
            ]
        ];
    }

    private function addItemsCargoInfo(Order $order, $response)
    {
        $products_status = $order->getProductsInCargoStatus();

        foreach($response['order']['items'] ?? [] as $key => $item) {
            if ($item['type'] == Cart::TYPE_PRODUCT && isset($products_status[$item['uniq']])) {
                $response['order']['items'][$key]['cargos_status'] = $products_status[$item['uniq']];
            }
        }

        return $response;
    }

}