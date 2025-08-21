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
use RS\Exception;
use Shop\Model\Orm\Order;

/**
 * Класс метода API для создания заказа на доставку
 */
class Create extends AbstractAuthorizedMethod
{
    const RIGHT_CREATE = 1;

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
            self::RIGHT_CREATE => t('Создание заказа на доставку')
        ];
    }

    /**
     * Создает заказ на доставку
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     *
     * @example GET /api/methods/deliveryOrder.create?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1577
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true,
                "delivery_order": {
                    "id": 43,
                    "order_id": "1577",
                    "delivery_type": "cdek_2_0",
                    "number": "610912-4771",
                    "creation_date": "2025-01-14 11:35:57",
                    "address": "350062, Россия, Краснодарский край, Краснодар, Красная, 180",
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
                            "value": "14.01.2025 11:35 - Принят - Офис СДЭК"
                        }
                    ],
                    "can_refresh": true,
                    "can_change": true,
                    "can_delete": true,
                    "track_number": null,
                    "actions": [
                        {
                            "title": "Печать квитанции к заказу",
                            "class": "btn-primary btn-alt",
                            "action": "print_order",
                            "attributes": {
                                "target": "_blank"
                            }
                        },
                        {
                            "title": "Печать ШК места к заказу",
                            "class": "btn-primary btn-alt",
                            "action": "print_barcode",
                            "attributes": {
                                "target": "_blank"
                            }
                        },
                        {
                            "title": "Вызов курьера",
                            "class": "btn-primary btn-alt crud-edit crud-sm-dialog",
                            "action": "call_courier"
                        },
                        {
                            "title": "Регистрация отказа",
                            "class": "btn-warning btn-alt crud-get",
                            "action": "refusal",
                            "confirm_text": "Вы действительно хотите зарегистрирвать отказ по данному заказу?"
                        }
                    ]
                }
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
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        try {
            $delivery_type = Get::getDeliveryTypeByOrder($order);
            $delivery_order = $delivery_type->createDeliveryOrder($order);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRITE_ERROR);
        }

        return [
            'response' => [
                'success' => true,
                'delivery_order' => Get::getDeliveryOrderData($delivery_order, $delivery_type)
            ]
        ];
    }
}