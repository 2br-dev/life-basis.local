<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;

/**
* Возвращает комплектации товара по ID товара
*/
class GetOffersList extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const RIGHT_LOAD = 1;
    
    protected $token_require = false;
    protected $costs_loaded = false; //Цены были уже загружены?
    protected $current_currency; //Текущая валюта
    /** @var \Catalog\Model\Orm\WareHouse[] $warehouses */
    protected $warehouses_by_id;

    /**
     * GetOffersList constructor.
     */
    function __construct()
    {
        parent::__construct();
        //Соберем склады
        $this->warehouses_by_id = \RS\Orm\Request::make()
                                ->from(new \Catalog\Model\Orm\WareHouse())
                                ->where([
                                    'site_id' => \RS\Site\Manager::getSiteId()
                                ])
                                ->objects(null, 'id');
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
            self::RIGHT_LOAD => t('Загрузка списка объектов'),
        ];
    }
    
    /**
    * Возвращает ORM объект с которым работаем
    * 
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\Product();
    }

    /**
     * Возвращает комплектации, многомерные комплектации или виртуальные многомерные комплектации товара по ID товара
     *
     * @param string $token Авторизационный токен
     * @param integer $product_id ID товара
     * @param array $sections дополнительные секции
     *
     * <b>sections</b>
     * stock - сведения по складам
     * costs - цены
     *
     * stick_info - имформация о складах и градации рисок
     * В зависимости от того, какой товар и что у него есть, вощвращается разный тип ответа в секции type и разные секции с информацией.
     * Возможные типы:
     * offer - комплектации
     * multioffers - многомерные комплектации без комплектаций
     * offers + multioffers - комплектации и многоморные комплектации
     * virtual multioffers - виртуальные многомерные комплектации
     *
     *
     * Возможные секции:
     * offers - комплектации
     * multioffers - многомерные комплектации или вирткльные многомерные компелектации
     * virtual_offers - виртальнык комлпектации (идут только в связке с виртуальными многомерными комлпектациями)
     *
     * @example GET api/methods/product.getofferslist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&product_id=1
     * Ответ
     * <pre>
     * {
     *        "response": {
     *            "no_product": true, //Если был сделан запрос к несуществующему товару
     *            "type": "offers + multioffers", //Тип возвращаемой информации (offer|multioffers|offers + multioffers|virtual multioffers)
     *            "stick_info": {
     *               "warehouses": [
     *                   {
     *                   "id": "1",
     *                       "title": "Метро Дворец Cпорта",
     *                       "alias": "mavi-step-trc-gulliver",
     *                       "image": {
     *                           "original_url": "http://192.168.1.199/storage/system/original/",
     *                           "big_url": "http://192.168.1.199/storage/system/resized/xy_1000x1000/_a7a4c5b4.jpg",
     *                           "middle_url": "http://192.168.1.199/storage/system/resized/xy_600x600/_22fa5e2.jpg",
     *                           "small_url": "http://192.168.1.199/storage/system/resized/xy_300x300/_6bf64320.jpg",
     *                           "micro_url": "http://192.168.1.199/storage/system/resized/xy_100x100/_8ae629de.jpg",
     *                           "nano_url": "http://192.168.1.199/storage/system/resized/xy_50x50/_8d50bd69.jpg"
     *                       },
     *                       "description": "<p>Наш склад находится в центре города. Предусмотрена удобная парковка для автомобилей и велосипедов.</p>\r\n<p><span style=\"color: #0000ff;\">Уважаемые наши покупатели, рекомендуем перед&nbsp; посещением магазина сделать следующие действия.</span></p>\r\n<p><span style=\"color: #0000ff;\">Позвонить по номеру &nbsp;телефона локального магазина <span>☎</span>&nbsp;<span style=\"text-decoration: underline;\">+380930186817</span>&nbsp; и получить консультацию по интересующем вас товарам или услугам.</span></p>\r\n<p><span style=\"color: #0000ff;\">Потом</span></p>\r\n<p><span style=\"color: #0000ff;\">Положить товары в корзину с указанием магазина в котором хотите купить этот товары. Или услугу.</span></p>\r\n<p><strong></strong><span style=\"color: #0000ff;\"></span></p>",
     *                       "adress": "Спортивная площадь, 1А",
     *                       "phone": "+380930186817",
     *                       "work_time": "с 10:00 до 22:00",
     *                       "coor_x": "50.4386",
     *                       "coor_y": "30.5229",
     *                       "default_house": "1",
     *                       "public": "1",
     *                       "checkout_public": "1",
     *                       "use_in_sitemap": "1",
     *                       "xml_id": null,
     *                       "meta_title": "",
     *                       "meta_keywords": "",
     *                       "meta_description": "",
     *                       "affiliate_id": "1"
     *                   }
     *               ],
     *               "stick_ranges": [
     *                   1,
     *                   2,
     *                   3,
     *                   4,
     *                   5
     *               ]
     *           },
     *            "offers": [ //Если есть комплектации
     *                {
     *                    "id": "1181",
     *                    "title": "Нетбук, DDR3",
     *                    "barcode": "ПФ-28",
     *                    "propsdata_arr": {
     *                        "Форм-фактор": "Нетбук",
     *                        "Тип памяти": "DDR3"
     *                    },
     *                    "num": "0", //Количество на складе общее для выбранной компелктации
     *                    "stock_num": [ //Наличие на определённом складе (Только если указан флаг - stock)
     *                        {
     *                           "warehouse_id": 1,
     *                           "num": "5.000"
     *                       },
     *                       {
     *                           "warehouse_id": 3,
     *                           "num": "1.000"
     *                       },
     *                       {
     *                       ...
     *                    ],
     *                    "stock_sticks": [ //Наличие на определённом складе в виде рисок (Только если указан флаг - stock)
     *                        {
     *                            "warehouse_id": 1,
     *                            "count": 0
     *                        },
     *                        {
     *                            "warehouse_id": 3,
     *                            "count": 1
     *                        },
     *                        ...
     *                    ]
     *                    "photos_arr": [
     *                        "2583",
     *                        "2584"
     *                    ],
     *                    "unit": "0",
     *                    "cost_values": { //Цена по умолчанию и зачеркнутая цены
     *                        "cost": "15590.00",
     *                        "old_cost": "0.00",
     *                        "cost_format": "15 590 р.",
     *                        "old_cost_format": "0 р."
     *                    },
     *                    "button_type": "buy" //Тип кнопки для показа для выбранной комплектации (buy|reservation|none) (купить|заказать|скрыть кнокпку)
     *                },
     *                ...
     *            ],
     *            "multioffers": [ //Многомерные комплектации или виртуальные многомерные комплектации
     *                {
     *                    "product_id": "616",
     *                    "prop_id": "8",
     *                    "title": "Тип памяти",
     *                    "is_photo": "1", //Если отображать как фото стоит у многомерной комплпектации
     *                    "sortn": "1",
     *                    "values": [
     *                        {
     *                            "id": "6",
     *                            "value": "DDR3",
     *                            "color" : "#ffffff",  //Только если тип цвет
     *                            "images": { //Может и не быть
     *                                "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/f/asft9ztxcacl124_4c7b0a96.jpg",
     *                                "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_200x200/f/asft9ztxcacl124_552ad92b.jpg",
     *                                "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/f/asft9ztxcacl124_54cdbe34.jpg",
     *                                "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/f/asft9ztxcacl124_54cdbe34.jpg",
     *                                "original_url": "http://mega.readyscript.ru/storage/photo/original/f/asft9ztxcacl124.jpg"
     *                            }
     *                        },
     *                        {
     *                            "id": "7",
     *                            "value": "DDR2",
     *                            "color" : "#000000", //Только если тип цвет
     *                            "images": { //Может и не быть
     *                                "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/d/79z2j7uyr69trcb_ff95897a.jpg",
     *                                "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_200x200/d/79z2j7uyr69trcb_e6c45ac7.jpg",
     *                                "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/d/79z2j7uyr69trcb_a555a0ec.jpg",
     *                                "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/d/79z2j7uyr69trcb_a555a0ec.jpg",
     *                                "original_url": "http://mega.readyscript.ru/storage/photo/original/d/79z2j7uyr69trcb.jpg"
     *                            }
     *                        }
     *                        ...
     *                    ],
     *                    "property_type": "list" //Тип отображения многомерной комплектации (list|radio|image|color)
     *                    ...
     *                }
     *                ...
     *            ],
     *            "virtual_offers": [ //Виртуальные комплектации
     *                {
     *                    "values": {
     *                        "Цвет": "Желтый",
     *                        "Размер": "8"
     *                    },
     *                    "product_id": 578
     *                },
     *                ...
     *            ]
     *        }
     *    }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($product_id, $token = null, $sections = ['stock', 'costs'])
    {
        //Загруженный товар
        $product  = new \Catalog\Model\Orm\Product($product_id);
        $response = parent::process($token, $product_id);
        
        if ($product['id']){
            unset($response['response']['product']); //Удалим конкретную информацию о товаре
            $this->current_currency = \Catalog\Model\CurrencyApi::getCurrentCurrency(); //Текущая валюта

            $response = \Catalog\Model\ApiUtils::getOffersInfo($product, $sections);
        }else{
            $response['response']['no_product'] = true;
            $response['response']['type']       = null;
            $response['response']['offers']     = [];
        }
        
        return $response;
    }
}