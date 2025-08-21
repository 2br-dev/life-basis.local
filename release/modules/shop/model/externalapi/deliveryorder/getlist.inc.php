<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\DeliveryOrder;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\DeliveryOrderApi;
use Shop\Model\DeliveryType\TraitInterfaceDeliveryOrder;
use Shop\Model\Orm\Order;

/**
 * Абстрактный класс для загрузки списка объектов
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
            self::RIGHT_LOAD => t('Загрузка данных')
        ];
    }

    /**
     * Возвращает список объектов
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param TraitInterfaceDeliveryOrder $delivery_type
     * @return array
     */
    public function getResultList($dao, $delivery_type)
    {
        $result = [];
        foreach($dao->getList() as $delivery_order) {
            $result[] = Get::getDeliveryOrderData($delivery_order, $delivery_type, false);
        }
        return $result;
    }

    /**
     * Возвращает список заказов на доставку
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа, для которого нужно вернуть заказы на доставку
     *
     * @example GET /api/methods/deliveryOrder.getList?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1577
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "delivery_order": [
                    {
                        "id": "42",
                        "order_id": "1577",
                        "delivery_type": "cdek_2_0",
                        "number": "610912-7103",
                        "creation_date": "2025-01-12 15:17:38",
                        "address": "350062, Россия, Краснодарский край, Краснодар, Красная, 180",
                        "can_refresh": true,
                        "can_change": true,
                        "can_delete": true,
                        "data_lines": [
                            {
                                "title": "Тариф",
                                "value": "Посылка склад-склад"
                            },
                            {
                                "title": "ПВЗ доставки",
                                "value": "KSD5"
                            },
                            {
                                "title": "Статус",
                                "value": "Принят"
                            },
                            {
                                "title": "История изменения статусов",
                                "value": "12.01.2025 15:17 - Принят - Офис СДЭК"
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
    protected function process($token, $order_id)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $delivery_type = Get::getDeliveryTypeByOrder($order);

        $delivery_order_api = new DeliveryOrderApi();
        $delivery_order_api->setFilter('order_id', $order['id']);

        return [
            'response' => [
                'delivery_order' => $this->getResultList($delivery_order_api, $delivery_type)
            ]
        ];
    }
}
