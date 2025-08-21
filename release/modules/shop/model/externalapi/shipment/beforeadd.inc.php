<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Shipment;

use Catalog\Model\ApiUtils;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Cart;
use Shop\Model\Orm\Order;
use Shop\Model\ShipmentApi;

/**
 * Метод, возвращает сведения, необходимые для создания нового документа "отгрузка".
 * Среди этих сведений:
 * - позиции заказа,
 * - количество уже отгруженных единиц,
 * - количество, которое еще предстоит отгрузить
 * - статус будущей отгрузки (полная отгрузка, частичная отгрузка)
 */
class BeforeAdd extends AbstractAuthorizedMethod
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
            self::RIGHT_LOAD => t('Загрузка сведений для будущей отгрузки')
        ];
    }

    /**
     * Подготавливает данные о товарах для отдачи через API
     *
     * @param $items
     * @return array
     */
    protected function getItems($data)
    {
        $result = [];

        foreach($data['items'] as $item) {
            $product = $item[Cart::TYPE_PRODUCT];
            $order_item = $item[Cart::CART_ITEM_KEY];

            $order_item_data = Get::prepareOrderItemForShipment($order_item, $product);

            foreach($order_item_data['uits'] as &$uit) {
                $check_result = $uit->getCheckResult();
                $uit['check_result'] = [
                    'status' => $check_result->getCheckStatus(),
                    'text' => $check_result->getCheckText(),
                    'color' => $check_result->getCheckStatusColor()
                ];
                $uit = array_diff_key($uit->getValues(), array_flip(['order_id', 'order_item_uniq']));
            }

            $result[] = $order_item_data;
        }

        return $result;
    }

    /**
     * Возвращает сведения, необходимые для создания новой отгрузки для заказа
     * ---
     * Анализирует все предыдущие отгрузки и возвращает информацию о количестве уже отгруженных единиц.
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     *
     * @example GET /api/help/shipment.beforeAdd?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1519
     *
     * Ответ:
     * <pre>
     * {
     *       "response": {
     *           "status": "can_partially_ship",
     *           "status_text": "Можно совершить частичную отгрузку",
     *           "order_num": "140283",
     *           "order_items": [
     *               {
     *                   "uniq": "e96c4ae104",
     *                   "type": "product",
     *                   "entity_id": "76636",
     *                   "offer": "7279",
     *                   "amount": 3,
     *                   "barcode": "68238-RFNPWYA",
     *                   "title": "Проектор Samsung SP-M220",
     *                   "model": "",
     *                   "single_weight": "100",
     *                   "single_cost": "21600.00",
     *                   "price": "64800.00",
     *                   "discount": "0.00",
     *                   "sortn": "0",
     *                   "profit": "19440.00",
     *                   "sku": "",
     *                   "unit_id": "1",
     *                   "extra_arr": {
     *                       "tax_ids": [
     *                           2,
     *                           5,
     *                           6,
     *                           7
     *                       ],
     *                       "unit": "шт."
     *                   },
     *                   "already_shipped": 0,
     *                   "amount_for_shipment": 3,
     *                   "uits": [
     *                       {
     *                           "id": "18",
     *                           "gtin": "04607809591508",
     *                           "serial": "sSBmxTYIFT(eq",
     *                           "other": "91FFD092test",
     *                           "is_shipped": false
     *                       }
     *                   ],
     *                   "multioffers_arr": [],
     *                   "has_marking": true,
     *                   "image": {
     *                       "id": "3138",
     *                       "title": "",
     *                       "original_url": "https://full.readyscript.local/storage/photo/original/a/xtrchptm9ydc034.jpg",
     *                       "big_url": "https://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/xtrchptm9ydc034_db4a69a7.jpg",
     *                       "middle_url": "https://full.readyscript.local/storage/photo/resized/xy_600x600/a/xtrchptm9ydc034_88b7f862.jpg",
     *                       "small_url": "https://full.readyscript.local/storage/photo/resized/xy_300x300/a/xtrchptm9ydc034_e16e1ea0.jpg",
     *                       "micro_url": "https://full.readyscript.local/storage/photo/resized/xy_100x100/a/xtrchptm9ydc034_7e745e.jpg",
     *                       "nano_url": "https://full.readyscript.local/storage/photo/resized/xy_50x50/a/xtrchptm9ydc034_6a95acdf.jpg"
     *                   }
     *               }
     *           ]
     *       }
     *   }
     * </pre>
     *
     * @return array Возвращает информацию, которая будет необходима для создания следующей отгрузки.
     *
     * Некоторые наиболее важные поля:
     *
     * <b>response.status</b> - идентификатор статуса следующей отгрузки
     * <b>response.status_text</b> - текстовое описание статуса следующей отгрузки
     * <b>response.order_items[].amount</b> - общее количество товарной позиции в заказе
     * <b>response.order_items[].already_shipped</b> - уже было отгружено в других документах "отгрузка"
     * <b>response.order_items[].amount_for_shipment</b> - количество единиц, которое может быть отгружено
     * <b>response.order_items[].uits[].is_shipped</b> - отгружена ли ранее данная маркировка. Если не отгружена, то ее еще можно удалить
     * <b>response.order_items[].has_marking</b> - есть ли у позиции маркировка
     *
     * Итоговая цена товарной позиции может быть вычислена по следующему правилу:
     * $total_price = response.order_items[].price - response.order_items[].discount
     */
    public function process($token, $order_id)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $shipment_api = new ShipmentApi();
        $data = $shipment_api->getNextShipmentData($order);
        $status = $shipment_api->getNextShipmentStatus($data);
        $status_text = $shipment_api->getShipmentStatusText($status);
        $status_bg_color = $shipment_api->getShipmentStatusBgColor($status);

        return [
            'response' => [
                'status' => $status,
                'status_text' => $status_text,
                'status_background_color' => $status_bg_color,
                'order_num' => $order['order_num'],
                'order_items' => $this->getItems($data),
            ]
        ];
    }
}