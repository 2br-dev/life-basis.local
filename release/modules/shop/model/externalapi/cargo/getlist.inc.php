<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cargo;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use Shop\Model\ExternalApi\Shipment\Get;
use Shop\Model\OrderCargoApi;
use Shop\Model\Orm\Order;
use ExternalApi\Model\Exception as ApiException;

/**
 * Метод API, возвращает список грузовых мест
 */
class GetList extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

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
            self::RIGHT_LOAD => t('Загрузка грузовых мест для заказа')
        ];
    }

    /**
     * Подготавливает данные о товарах для API
     *
     * @param array $order_items
     * @return array
     */
    protected function prepareOrderItemsForApi($order_items)
    {
        $result = [];
        foreach($order_items as $item_data) {
            $order_item_data = Get::prepareOrderItemForShipment($item_data['cartitem'], $item_data['product']);
            $item = [
                'order_item' => $order_item_data,
                'uit' => isset($item_data['uit']) ? Utils::extractOrm($item_data['uit']) : null,
                'max_amount' => $item_data['max_amount'],
            ];

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Подготавливает данные о грузовых местах для API
     *
     * @param array $cargos
     * @return array
     */
    public static function prepareCargosForApi($cargos)
    {
        $result = [];
        foreach($cargos as $cargo) {
            $item = Utils::extractOrm($cargo);
            $item['items'] = Utils::extractOrmList($cargo->getCargoItems());
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Возвращает список товаров для грузовых мест, а также список уже созданных грузовых мест для заказа
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     *
     * @example GET api/methods/cargo.getList?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1563
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "order_num": "124945",
                "order_items": [
                    {
                        "order_item": {
                            "uniq": "0504db4321",
                            "type": "product",
                            "entity_id": "76636",
                            "offer": "7279",
                            "amount": 3,
                            "barcode": "68238-RFNPWYA",
                            "title": "Проектор Samsung SP-M220",
                            "model": "",
                            "single_weight": "100",
                            "single_cost": "21600.00",
                            "price": "64800.00",
                            "discount": "0.00",
                            "sortn": "0",
                            "profit": "19440.00",
                            "sku": "4607809591508",
                            "unit_id": "1",
                            "extra_arr": {
                                "tax_ids": [
                                    "7"
                                ],
                                "unit": "шт."
                            },
                            "multioffers_arr": [],
                            "has_marking": true,
                            "image": {
                                "id": "3138",
                                "title": "",
                                "original_url": "https://full.readyscript.local/storage/photo/original/a/xtrchptm9ydc034.jpg",
                                "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/xtrchptm9ydc034_db4a69a7.jpg",
                                "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/a/xtrchptm9ydc034_88b7f862.jpg",
                                "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/a/xtrchptm9ydc034_e16e1ea0.jpg",
                                "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/a/xtrchptm9ydc034_7e745e.jpg",
                                "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/a/xtrchptm9ydc034_6a95acdf.jpg"
                            }
                        },
                        "uit": {
                            "id": "57",
                            "gtin": "04670540176099",
                            "serial": "5LnOjv",
                            "other": "93dGVz"
                        },
                        "max_amount": 1
                    },
                    {
                        "order_item": {
                            "uniq": "0504db4321",
                            "type": "product",
                            "entity_id": "76636",
                            "offer": "7279",
                            "amount": 3,
                            "barcode": "68238-RFNPWYA",
                            "title": "Проектор Samsung SP-M220",
                            "model": "",
                            "single_weight": "100",
                            "single_cost": "21600.00",
                            "price": "64800.00",
                            "discount": "0.00",
                            "sortn": "0",
                            "profit": "19440.00",
                            "sku": "4607809591508",
                            "unit_id": "1",
                            "extra_arr": {
                                "tax_ids": [
                                    "7"
                                ],
                                "unit": "шт."
                            },
                            "multioffers_arr": [],
                            "has_marking": true,
                            "image": {
                                "id": "3138",
                                "title": "",
                                "original_url": "https://full.readyscript.local/storage/photo/original/a/xtrchptm9ydc034.jpg",
                                "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/xtrchptm9ydc034_db4a69a7.jpg",
                                "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/a/xtrchptm9ydc034_88b7f862.jpg",
                                "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/a/xtrchptm9ydc034_e16e1ea0.jpg",
                                "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/a/xtrchptm9ydc034_7e745e.jpg",
                                "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/a/xtrchptm9ydc034_6a95acdf.jpg"
                            }
                        },
                        "uit": {
                            "id": "56",
                            "gtin": "04670540176099",
                            "serial": "5'W9Um",
                            "other": "93dGVz"
                        },
                        "max_amount": 1
                    },
                    {
                        "order_item": {
                            "uniq": "0504db4321",
                            "type": "product",
                            "entity_id": "76636",
                            "offer": "7279",
                            "amount": 3,
                            "barcode": "68238-RFNPWYA",
                            "title": "Проектор Samsung SP-M220",
                            "model": "",
                            "single_weight": "100",
                            "single_cost": "21600.00",
                            "price": "64800.00",
                            "discount": "0.00",
                            "sortn": "0",
                            "profit": "19440.00",
                            "sku": "4607809591508",
                            "unit_id": "1",
                            "extra_arr": {
                                "tax_ids": [
                                    "7"
                                ],
                                "unit": "шт."
                            },
                            "multioffers_arr": [],
                            "has_marking": true,
                            "image": {
                                "id": "3138",
                                "title": "",
                                "original_url": "https://full.readyscript.local/storage/photo/original/a/xtrchptm9ydc034.jpg",
                                "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/xtrchptm9ydc034_db4a69a7.jpg",
                                "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/a/xtrchptm9ydc034_88b7f862.jpg",
                                "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/a/xtrchptm9ydc034_e16e1ea0.jpg",
                                "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/a/xtrchptm9ydc034_7e745e.jpg",
                                "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/a/xtrchptm9ydc034_6a95acdf.jpg"
                            }
                        },
                        "uit": null,
                        "max_amount": 1
                    }
                ],
                "cargos": [
                    {
                        "id": "60",
                        "order_id": "1563",
                        "title": "Коробка 30x30",
                        "width": "300",
                        "height": "300",
                        "dept": "300",
                        "weight": "200",
                        "items": [
                            {
                                "id": "100",
                                "order_id": "1563",
                                "order_cargo_id": "60",
                                "order_item_uniq": "0504db4321",
                                "order_item_uit_id": "0",
                                "amount": "1.000"
                            }
                        ]
                    },
                    {
                        "id": "61",
                        "order_id": "1563",
                        "title": "Коробка 20x20",
                        "width": "200",
                        "height": "200",
                        "dept": "200",
                        "weight": "100",
                        "items": [
                            {
                                "id": "101",
                                "order_id": "1563",
                                "order_cargo_id": "61",
                                "order_item_uniq": "0504db4321",
                                "order_item_uit_id": "0",
                                "amount": "1.000"
                            }
                        ]
                    }
                ]
            }
        }
     * </pre>
     *
     * @return array
     */
    public function process($token, $order_id)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ с ID %0 не найден', [$order_id]), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $order_cargo_api = new OrderCargoApi();
        $order_items = $order_cargo_api->getCargoOrderItems($order);
        $order_items_data = $this->prepareOrderItemsForApi($order_items);

        $order_cargo_api->setFilter('order_id', $order_id);
        $cargos = $order_cargo_api->getList();
        $cargos_data = self::prepareCargosForApi($cargos);

        return [
            'response' => [
                'order_num' => $order['order_num'],
                'order_items' => $order_items_data,
                'cargos' => $cargos_data
            ]
        ];
    }
}