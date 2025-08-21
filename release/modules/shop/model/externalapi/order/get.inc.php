<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use Catalog\Model\Orm\WareHouse;
use ExternalApi\Model\Exception as ApiException;
use Partnership\Model\Orm\Partner;
use Shop\Model\ApiUtils;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\UserStatus;
use Site\Model\Orm\Site;
use Users\Model\Orm\User;

/**
* Загружает объект
*/
class Get extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const RIGHT_COURIER = 2;
    const RIGHT_FULL_ACCESS = 3;

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
            self::RIGHT_LOAD => t('Загрузка своих заказов'),
            self::RIGHT_FULL_ACCESS => t('Загрузка всех заказов'),
            self::RIGHT_COURIER => t('Загрузка курьерских заказов')
        ];
    }

    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new Order();
    }

    /**
    * Возвращает объект "Заказ"
    *
    * @param string $token Авторизационный токен
    * @param integer $order_id ID заказа
    * @param integer $ignore_user_group флаг указывает на, то игнорировать ли установленную группу у пользователя. Группа пользователя сужает доступный список заказов в соотвествиии с установленной группой.
    *
    * @example GET /api/methods/order.get?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&order_id=1
    *
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "order": {
    *             "id": "159",
    *             "order_num": "159",
    *             "user_id": "2",
    *             "currency": "RUB",
    *             "currency_ratio": "1",
    *             "currency_stitle": "р.",
    *             "ip": "127.0.0.1",
    *             "track_url": "http://edostavka.ru/look/34098435809",
    *             "dateof": "2016-08-31 14:13:06",
    *             "dateof_date": "31.08.2016",
    *             "dateof_datetime": "31.08.2016 11:05",
    *             "dateofupdate": "2016-09-22 01:00:27",
    *             "totalcost": "1468.95",
    *             "profit": "175.00",
    *             "user_delivery_cost": "0.00",
    *             "is_payed": "0",
    *             "status": "1",
    *             "admin_comments": "",
    *             "user_text": "",
    *             "hash": "0406df2de33b39217140f5b43bc664e6",
    *             "contact_person": "",
    *             "use_addr": "1",
    *             "only_pickup_points": null,
    *             "userfields_arr": [],
    *             "delivery": "2",
    *             "deliverycost": null,
    *             "courier_id": "2",
    *             "warehouse": "6",
    *             "payment": "1",
    *             "comments": "",
    *             "user_fio": null,
    *             "user_email": null,
    *             "user_phone": null,
    *             "partner_id": null,
    *             "dateof_iso": "2016-08-31T14:13:06+03:00",
    *             "items": [
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "vf4dtvv2ml",
    *                     "type": "product",
    *                     "entity_id": "134",
    *                     "multioffers": false,
    *                     "offer": "4",
    *                     "amount": "1",
    *                     "barcode": "ALI-W-36-1",
    *                     "title": "Коньки фигурные Nordway Alice",
    *                     "model": "new2",
    *                     "single_weight": "0",
    *                     "single_cost": "1749.00",
    *                     "price": "1749.00",
    *                     "price_formatted": "1 749  р."
    *                     "profit": "175.00",
    *                     "discount": "350.00",
    *                     "sortn": "0",
    *                     "data": {
    *                         "tax_ids": [
    *                             "2",
    *                             "5"
    *                         ],
    *                         "unit": "шт."
    *                     },
    *                     "image": {
    *                         "id": "2361",
    *                         "title": null,
    *                         "original_url": "http://mega.readyscript.ru/storage/photo/original/i/06eq2uxurfz9l3n.jpg",
    *                         "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_1000x1000/i/06eq2uxurfz9l3n_c2e6b23d.jpg",
    *                         "middle_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/i/06eq2uxurfz9l3n_ce60b3b1.jpg",
    *                         "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_300x300/i/06eq2uxurfz9l3n_a7b95573.jpg",
    *                         "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                         "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                     }
    *                     "url": "http://full.readyscript.local/product/134/"
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "9ff8c24e4d",
    *                     "type": "coupon",
    *                     "entity_id": "1",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Купон на скидку demo",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "0.00",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "1",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "042ykmj1v7",
    *                     "type": "subtotal",
    *                     "entity_id": null,
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Товаров на сумму",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "1175.63",
    *                     "profit": "0.00",
    *                     "discount": "1175.63",
    *                     "sortn": "2",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "ssyudt964c",
    *                     "type": "tax",
    *                     "entity_id": "2",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "НДС, 18%(включен в стоимость)",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "223.37",
    *                     "profit": "0.00",
    *                     "discount": "223.37",
    *                     "sortn": "3",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "2jukioa3c1",
    *                     "type": "tax",
    *                     "entity_id": "5",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "НДС, 10%(включен в стоимость)",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "0.00",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "4",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "ykf11msejt",
    *                     "type": "delivery",
    *                     "entity_id": "2",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Доставка: Доставка по г.Краснодару",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "0.00",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "5",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "ojbz3ku152",
    *                     "type": "",
    *                     "entity_id": "1",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Комиссия при оплате через Безналичный расчет 5%",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "69.95",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "6",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 }
    *             ]
    *         },
    *         "user": {
    *             "2": {
    *                 "id": "2",
    *                 "name": "Артем",
    *                 "surname": "Иванов",
    *                 "midname": "Петрович",
    *                 "e_mail": "mail@readyscript.ru",
    *                 "login": "demo@example.com",
    *                 "phone": "+700000000000",
    *                 "sex": "",
    *                 "subscribe_on": "0",
    *                 "dateofreg": "0000-00-00 00:00:00",
    *                 "ban_expire": null,
    *                 "last_visit": "2016-09-22 11:33:19",
    *                 "is_company": "1",
    *                 "company": "ООО Ромашка",
    *                 "company_inn": "1234567890",
    *                 "data": {
    *                     "passport": "00000012233"
    *                 },
    *                 "passport": "серия 03 06, номер 123456, выдан УВД Западного округа г. Краснодар, 04.03.2006",
    *                 "company_kpp": "0987654321",
    *                 "company_ogrn": "1234567890",
    *                 "company_v_lice": "директора Сидорова Семена Петровича",
    *                 "company_deistvuet": "устава",
    *                 "company_bank": "ОАО УРАЛБАНК",
    *                 "company_bank_bik": "1234567890",
    *                 "company_bank_ks": "10293847560192837465",
    *                 "company_rs": "19283746510293847560",
    *                 "company_address": "350089, г. Краснодар, ул. Чекистов, 12",
    *                 "company_post_address": "350089, г. Краснодар, ул. Чекистов, 15",
    *                 "company_director_post": "директор",
    *                 "company_director_fio": "Сидоров С.П.",
    *                 "user_cost": null
    *             }
    *         },
    *         "status": {
    *             "1": {
    *                 "id": "1",
    *                 "title": "Новый",
    *                 "bgcolor": "#83b7b3",
    *                 "type": "new"
    *             }
    *         },
    *         "address": {
    *             "1": {
    *                 "id": "1",
    *                 "user_id": "2",
    *                 "order_id": "0",
    *                 "zipcode": "350000",
    *                 "country": "Россия",
    *                 "region": "Краснодарский край",
    *                 "city": "Краснодар",
    *                 "address": "ул. Тестовая, 404, кв. 503",
    *                 "city_id": "307",
    *                 "region_id": "13",
    *                 "country_id": "1",
    *                 "deleted": "0"
    *             }
    *         },
    *         "warehouse": {
    *             "6": {
    *                 "id": "6",
    *                 "title": "Розничный склад",
    *                 "alias": "roznichnyy-sklad",
    *                 "image": null,
    *                 "description": null,
    *                 "adress": null,
    *                 "phone": null,
    *                 "work_time": null,
    *                 "coor_x": "55.7533",
    *                 "coor_y": "37.6226",
    *                 "default_house": "0",
    *                 "public": null,
    *                 "checkout_public": null,
    *                 "use_in_sitemap": "0",
    *                 "xml_id": "3564ef2c-517e-11e6-8505-001a7dda7113",
    *                 "meta_title": null,
    *                 "meta_keywords": null,
    *                 "meta_description": null,
    *                 "affiliate_id": "0"
    *             }
    *         },
    *         "delivery": {
    *             "2": {
    *                 "id": "2",
    *                 "title": "Доставка по г.Краснодару",
    *                 "description": "Доставка осуществляется на следующие день после оплаты заказа",
    *                 "picture": "",
    *                 "xzone": null,
    *                 "min_price": "0",
    *                 "max_price": "0",
    *                 "min_cnt": "0",
    *                 "first_status": "0",
    *                 "user_type": "all",
    *                 "extrachange_discount": "0",
    *                 "public": "1",
    *                 "class": "fixedpay",
    *                 "show_in_cost_block": "0"
    *             }
    *         },
    *         "payment": {
    *             "1": {
    *                 "id": "1",
    *                 "title": "Безналичный расчет",
    *                 "description": "Оплата должна производиться с расчетного счета предприятия",
    *                 "picture": null,
    *                 "first_status": "0",
    *                 "success_status": "0",
    *                 "user_type": "all",
    *                 "target": "all",
    *                 "delivery": [],
    *                 "public": "1",
    *                 "default_payment": "1",
    *                 "commission": "5",
    *                 "docs": [
    *                   {
    *                       "title": "Счёт",
    *                       "link": "http://mega.readyscript.ru/files/bills/06eq2uxurfz9l3n/",
    *                   }
    *                 ]
    *                 "class": "bill"
    *             }
    *         },
    *         "site_uid": "7e94c1bc56a2984e6a86b45d2bc3e7149959f3ed"
    *     }
    * }
    * </pre>
    *
    * @return array Возвращает объект заказа и все связанные с ним объекты из справочников
    */
    protected function process($token, $order_id, $ignore_user_group = 0)
    {
        /**
        * @var Order $object
        */
        $object = $this->getOrmObject();

        if ($object->load($order_id)) {
            //Курьер может просматривать только свои заказы
            if ($this->checkAccessError(self::RIGHT_COURIER) === false && !$ignore_user_group) {
                if ($object['courier_id'] != $this->token['user_id']) {
                    throw new ApiException(t('Курьеры могут загружать только назначенные им заказы'), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
            }

            if ($this->checkAccessError(self::RIGHT_FULL_ACCESS) !== false) {
                if ($object['user_id'] != $this->token['user_id']) {
                    throw new ApiException(t('Допустимо загружать только свои заказы'), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
            }

            $site = new Site($object['__site_id']->get());
            $object = ApiUtils::addOrderItems($object);
            $response = ApiUtils::getFullOrderResponse($object);

            return [
                'response' => [
                    ...$response,
                    'site_uid' => $site->getSiteHash()
                ]
            ];
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}
