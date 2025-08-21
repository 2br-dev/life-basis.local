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
use ExternalApi\Model\Utils;
use Shop\Model\DeliveryType\AbstractType;
use Shop\Model\DeliveryType\InterfaceDeliveryOrder;
use Shop\Model\DeliveryType\TraitInterfaceDeliveryOrder;
use Shop\Model\Orm\DeliveryOrder;
use Shop\Model\Orm\Order;

/**
 * Класс метода API для получения информации по одному заказу на доставку
 */
class Get extends AbstractAuthorizedMethod
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
            self::RIGHT_LOAD => t('Загрузка заказа на доставку')
        ];
    }

    /**
     * Возвращает сведения по одному заказу на доставку
     *
     * @param DeliveryOrder $delivery_order
     * @param TraitInterfaceDeliveryOrder $delivery_type_object
     * @param bool $detail_mode Если true, то возвращает более подробную информацию о возможных действиях с заказом
     * @return array
     */
    public static function getDeliveryOrderData(DeliveryOrder          $delivery_order,
                                                InterfaceDeliveryOrder $delivery_type_object,
                                                                       $detail_mode = true)
    {
        $data = Utils::extractOrm($delivery_order);
        $data['data_lines'] = $delivery_order->getDataLines();
        $data['can_refresh'] = $delivery_type_object->canRefreshDeliveryOrder();
        $data['can_change'] = $delivery_type_object->canChangeDeliveryOrder();
        $data['can_delete'] = $delivery_type_object->canDeleteDeliveryOrder();

        if ($detail_mode) {
            $data['track_number'] = $delivery_order->getDeliveryOrderTrackNumber();
            $data['actions'] = array_filter($delivery_order->getActions(), function($action) {
                return (in_array(($action['view_type'] ?? ''), ['message', 'output']));
            });
        }

        return $data;
    }


    /**
     * Возвращает тип доставки, поддерживающий оформление заказов
     *
     * @param Order $order
     * @return AbstractType|InterfaceDeliveryOrder|TraitInterfaceDeliveryOrder
     */
    public static function getDeliveryTypeByOrder(Order $order)
    {
        $delivery = $order->getDelivery();
        if (!$delivery['id']) {
            throw new ApiException(t('Доставка не найдена'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $delivery_type = $delivery->getTypeObject();
        if (!($delivery_type instanceof InterfaceDeliveryOrder)) {
            throw new ApiException(t('Расчетный класс доставки не поддерживает оформление заказа'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        return $delivery_type;
    }

    /**
     * Возвращает тип доставки, поддерживающий оформление заказов
     *
     * @param Order $order
     * @return array
     */
    public static function getDeliveryTypeByDeliveryOrderId($delivery_order_id)
    {
        $delivery_order = new DeliveryOrder($delivery_order_id);
        if (!$delivery_order['id']) {
            throw new ApiException(t('Заказ на доставку не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $order = $delivery_order->getOrder();
        if (!$order['id']) {
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        return [$delivery_order, self::getDeliveryTypeByOrder($order), $order];
    }

    /**
     * Возвращает подробную информацию по одному заказу на доставку
     *
     * @param string $token Авторизационный токен
     * @param integer $delivery_order_id Идентификатор заказа на доставку
     *
     * @example GET /api/methods/deliveryorder.get?token=311211047ab5474dd67ef88345313a6e479bf616&delivery_order_id=42
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "delivery_order": {
                    "id": "42",
                    "order_id": "1577",
                    "delivery_type": "cdek_2_0",
                    "number": "610912-7103",
                    "creation_date": "2025-01-12 15:17:38",
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
                            "value": "12.01.2025 15:17 - Принят - Офис СДЭК"
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
                            "view_type": "output",
                            "attributes": {
                                "target": "_blank"
                            }
                        },
                        {
                            "title": "Печать ШК места к заказу",
                            "class": "btn-primary btn-alt",
                            "action": "print_barcode",
                            "view_type": "output",
                            "attributes": {
                                "target": "_blank"
                            }
                        },
                        {
                            "title": "Регистрация отказа",
                            "class": "btn-warning btn-alt crud-get",
                            "action": "refusal",
                            "view_type": "message",
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
    function process($token, $delivery_order_id)
    {
        [$delivery_order, $delivery_type] = self::getDeliveryTypeByDeliveryOrderId($delivery_order_id);

        return [
            'response' => [
                'delivery_order' => self::getDeliveryOrderData($delivery_order, $delivery_type),
            ]
        ];
    }
}