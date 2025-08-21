<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Marking;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use RS\Exception;
use Shop\Model\ApiUtils;
use Shop\Model\ShipmentApi;

class FindItem extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

    const STATUS_FOUND = 'found';
    const STATUS_FOUND_MANY = 'found_many';
    const STATUS_NOT_FOUND = 'not_found';

    /**
     * Возвращает текстовое описание статуса
     *
     * @param string $status
     * @return string
     */
    public function getStatusTitle($status)
    {
        $titles = [
            self::STATUS_FOUND => t('Товар найден'),
            self::STATUS_FOUND_MANY => t('Найдено более одного товара, выберите товар вручную'),
            self::STATUS_NOT_FOUND => t('Товар не найден, выберите товар вручную'),
        ];

        return $titles[$status] ?? $status;
    }

    /**
     * Возвращает цвет фона для статуса
     *
     * @param $status
     * @return string
     */
    public function getStatusBackgroundColor($status)
    {
        $colors = [
            self::STATUS_FOUND => '#5cb85c',
            self::STATUS_FOUND_MANY => '#f0ad4e',
            self::STATUS_NOT_FOUND => '#d9534f',
        ];

        return $colors[$status] ?? '';
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
            self::RIGHT_LOAD => t('Поиск товара по маркировке')
        ];
    }

    /**
     * Возвращает массив, готовый для выдачи со списком OrderItem
     *
     * @param $order_items
     * @return array
     */
    public function prepareOrderItems($order_items)
    {
        ApiUtils::appendRuntimeOrderItemProperties();

        $result = [];
        foreach($order_items as $order_item) {
            $result[] = ApiUtils::extractOrderItem($order_item);
        }

        return $result;
    }

    /**
     * Ищет подходящий товар в заказе по маркировке. Используется поиск по GTIN (штрихкоду)
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID Заказа
     * @param string $datamatrix Код маркировки
     * @return array Возвращает найденные товарные позиции (OrderItem) в заказе и статус
     *
     * @example GET /api/methods/marking.finditem?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1546&datamatrix=010460780959150821sSBmxTYIFT(eq91FFD092testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "datamatrix": {
                    "gtin": "4607809591508"
                },
                "status": {
                    "id": "found",
                    "title": "Товар найден",
                    "background_color": "#5cb85c"
                },
                "order_items": [
                    {
                        "uniq": "e83af74425",
                        "type": "product",
                        "entity_id": "76636",
                        "offer": "7279",
                        "multioffers": [],
                        "amount": 1,
                        "title": "Проектор Samsung SP-M220",
                        "extra_arr": {
                            "tax_ids": [
                                "7"
                            ],
                            "unit": "шт."
                        },
                        "order_id": "1546",
                        "barcode": "68238-RFNPWYA",
                        "sku": "4607809591508",
                        "model": "",
                        "single_weight": "100",
                        "single_cost": "21600.00",
                        "price": "21600.00",
                        "profit": "6480.00",
                        "discount": "0.00",
                        "unit_id": "1",
                        "sortn": "0",
                        "image": {
                            "id": "3138",
                            "title": "",
                            "original_url": "https://full.readyscript.local/storage/photo/original/a/xtrchptm9ydc034.jpg",
                            "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/xtrchptm9ydc034_db4a69a7.jpg",
                            "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/a/xtrchptm9ydc034_88b7f862.jpg",
                            "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/a/xtrchptm9ydc034_e16e1ea0.jpg",
                            "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/a/xtrchptm9ydc034_7e745e.jpg",
                            "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/a/xtrchptm9ydc034_6a95acdf.jpg"
                        },
                        "url": "https://full.readyscript.local/product/proektor-samsung-sp-m220/",
                        "cost": 21600,
                        "cost_formatted": "21 600 ₽",
                        "price_formatted": "21 600 ₽",
                        "discount_formatted": "0 ₽",
                        "unit_title": "шт.",
                        "multioffers_values": [],
                        "offer_type": "single"
                    }
                ]
            }
        }
     * </pre>
     */
    function process($token, $order_id, $datamatrix)
    {
        try {
            $shipment_api = new ShipmentApi();
            $gtin = $shipment_api->getGtinByDatamatrix(ApiUtils::prepareMobileDataMatrix($datamatrix));
            $order_items = $shipment_api->getOrderItemsByDataMatrix($order_id, $gtin);

            if (!count($order_items)) {
                $status = self::STATUS_NOT_FOUND;
            } else if (count($order_items) == 1) {
                $status = self::STATUS_FOUND;
            } else {
                $status = self::STATUS_FOUND_MANY;
            }
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        return [
            'response' => [
                'datamatrix' => [
                    'gtin' => $gtin
                ],
                'status' => [
                    'id' => $status,
                    'title' => $this->getStatusTitle($status),
                    'background_color' => $this->getStatusBackgroundColor($status)
                ],
                'order_items' => $this->prepareOrderItems($order_items)
            ]
        ];
    }
}