<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Shipment;

use Catalog\Model\ApiUtils;
use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractGet;
use ExternalApi\Model\Utils;
use RS\Helper\CustomView;
use Shop\Model\Orm\Shipment;
use Shop\Model\Orm\ShipmentItem;
use RS\Orm\Type;

/**
 * Возвращает объект отгрузки по ID
 */
class Get extends AbstractGet
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
            self::RIGHT_LOAD => t('Загрузка объекта')
        ];
    }

    /**
     * Возвращает название секции ответа, в которой должен вернуться список объектов
     *
     * @return string
     */
    public function getObjectSectionName()
    {
        return 'shipment';
    }

    /**
     * Возвращает объект с которым работает
     *
     */
    public function getOrmObject()
    {
        return new Shipment();
    }

    /**
     * Подготавливает данные OrderItem для отгрузки
     *
     * @param $order_item
     * @param $product
     * @return array
     */
    public static function prepareOrderItemForShipment($order_item, $product)
    {
        $order_item_data = array_diff_key($order_item->getValues(), array_flip(['extra',
            'order_id',
            'shipment_warehouse_name',
            'shipment_warehouse_id',
            'multioffers']));

        $order_item_data['multioffers_arr'] = @unserialize($order_item['multioffers']) ?: [];
        if ($product) {
            $order_item_data['has_marking'] = $product['marked_class'] != '';
            $order_item_data['image'] = ApiUtils::prepareImagesSection($product->getMainImage());
        }
        return $order_item_data;
    }

    /**
     * Возвращает объект отгрузки по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $shipment_id ID отгрузки
     *
     * @example GET api/methods/reservation.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&id=1
     * Ответ
     * <pre>
     * {
            "response": {
                "shipment": {
                    "id": "40",
                    "date": "2024-10-16 10:55:48",
                    "info_order_num": "485049",
                    "info_total_sum": "4388.00",
                    "items": [
                        {
                            "uniq": "42d95c1651",
                            "type": "product",
                            "entity_id": "78574",
                            "offer": "10481",
                            "amount": "1.000",
                            "barcode": "509-080-204-030",
                            "title": "Конструктор LEGO Duplo Считай и играй 10558",
                            "model": "",
                            "single_weight": "100",
                            "single_cost": "1500.00",
                            "price": "1500.00",
                            "discount": "0.00",
                            "sortn": "5",
                            "profit": "450.00",
                            "sku": "",
                            "unit_id": "1",
                            "extra_arr": {
                                "tax_ids": [
                                    2,
                                    5,
                                    6,
                                    7
                                ],
                                "unit": "шт."
                            },
                            "multioffers_arr": [],
                            "has_marking": false,
                            "image": {
                                "id": "10549",
                                "title": "",
                                "original_url": "https://full.readyscript.local/storage/photo/original/d/f6h5oofh4bc4i24.png",
                                "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/d/f6h5oofh4bc4i24_427497dc.png",
                                "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/d/f6h5oofh4bc4i24_ff4d0b89.png",
                                "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/d/f6h5oofh4bc4i24_9694ed4b.png",
                                "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/d/f6h5oofh4bc4i24_778487b5.png",
                                "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/d/f6h5oofh4bc4i24_cb11d7c5.png"
                            },
                            "uit": null
                        },
                        {
                            "uniq": "9d6f172e9b",
                            "type": "product",
                            "entity_id": "78575",
                            "offer": "10482",
                            "amount": "1.000",
                            "barcode": "601-060-707-050",
                            "title": "Конструктор LEGO Ninjago Храм Света 70505",
                            "model": "",
                            "single_weight": "100",
                            "single_cost": "2888.00",
                            "price": "2888.00",
                            "discount": "0.00",
                            "sortn": "7",
                            "profit": "866.00",
                            "sku": "",
                            "unit_id": "1",
                            "extra_arr": {
                                "tax_ids": [
                                    2,
                                    5,
                                    6,
                                    7
                                ],
                                "unit": "шт."
                            },
                            "multioffers_arr": [],
                            "has_marking": false,
                            "image": {
                                "id": "10554",
                                "title": "",
                                "original_url": "https://full.readyscript.local/storage/photo/original/h/vyx4le1o4uax3qf.png",
                                "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/h/vyx4le1o4uax3qf_a653b66.png",
                                "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/h/vyx4le1o4uax3qf_d9bcd8eb.png",
                                "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/h/vyx4le1o4uax3qf_b0653e29.png",
                                "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/h/vyx4le1o4uax3qf_517554d7.png",
                                "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/h/vyx4le1o4uax3qf_2a6779fd.png"
                            },
                            "uit": null
                        }
                    ]
                }
            }
        }
     * </pre>
     * @return array Возвращает развернутые сведения по отгрузке. Товары в items с маркировками будут представлены отдельными элементами с одинаковыми uniq и разными uit.
     *
     * @throws \ExternalApi\Model\Exception
     */
    function process($token, $shipment_id)
    {
        $response = parent::process($token, $shipment_id);

        if ($this->object['id']) {
            $base_currency = CurrencyApi::getBaseCurrency()->stitle;
            $items = $this->object->getShipmentItems();
            (new ShipmentItem())->getPropertyIterator()->append([
                'uit' => (new Type\ArrayList())
                    ->setVisible(true, 'app'),
                'order_item' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
            ]);
            $result_items = [];
            foreach($items as $item) {
                $order_item = $item->getOrderItem();
                $product = $order_item->getProduct();

                if (!$order_item['uniq']) {
                    $order_item['title'] = t('Товар удален из заказа');
                }

                $one_item = self::prepareOrderItemForShipment($order_item, $product);

                //Удаляем цены, чтобы было однозначно, что в API необходимо использовать price.
                //Ведь здесь в ответе мы предоставляем расширенный вариант объектов отгруженных товаров ShipmentItem, а не OrderItem
                unset($one_item['discount']);
                unset($one_item['single_cost']);

                $one_item['price'] = $item['cost'];
                $one_item['price_formatted'] = CustomView::cost($one_item['price'], $base_currency);
                $one_item['amount'] = (float)$item['amount'];
                $one_item['uit'] = ($uit = $item->getUit()) ? Utils::extractOrm($uit) : null;
                $result_items[] = $one_item;
            }

            //Сортируем, чтобы доставка была где-то в конце списка
            usort($result_items, function($a, $b) {
                return $a['sortn'] <=> $b['sortn'];
            });

            $response['response']['shipment']['order_items'] = $result_items;
            $response['response']['shipment']['info_total_sum_formatted'] = CustomView::cost($response['response']['shipment']['info_total_sum'], $base_currency);
        }

        return $response;
    }
}
