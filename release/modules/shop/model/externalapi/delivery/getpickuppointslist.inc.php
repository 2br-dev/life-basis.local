<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Delivery;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use ExternalApi\Model\Exception as ApiException;

/**
 * Метод API, возвращает пункты выдачи для заданного адреса.
 */
class GetPickupPointsList extends AbstractAuthorizedMethod
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
            self::RIGHT_LOAD => t('Загрузка списка пунктов выдачи'),
        ];
    }

    /**
     * Возвращает пункты выдачи для заданной доставки и адреса.
     * ---
     * В случае, если ПВЗ не поддерживаются способом доставки, будет возвращена ошибка.
     * В случае, если ПВЗ поддерживаются, но в выбранном городе нет пунктов, будет возвращен пустой массив.
     *
     * @param string $token Авторизационный токен
     * @param integer $delivery_id ID доставки
     * @param integer $address_id ID адреса
     *
     * @return array
     * @example GET delivery.getPickupPointsList?token=311211047ab5474dd67ef88345313a6e479bf616&address_id=303&delivery_id=10
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "list":[
                    {
                        "code": "KSD215",
                        "title": "KSD215, Краснодар, ул. Петра Метальников",
                        "country": "RU",
                        "region": "Краснодарский край",
                        "city": "Краснодар",
                        "address": "ул. Петра Метальников, 5к3",
                        "phone": "+74950090405",
                        "worktime": "Пн-Вс 09:00-21:00",
                        "coord_x": 39.00915,
                        "coord_y": 45.093983,
                        "note": "",
                        "cost": 0,
                        "payment_by_cards": true,
                        "preset": "islands#redIcon",
                        "extra": []
                    }
              ]
            }
     * }
     * </pre>
     */
    function process($token, $delivery_id, $address_id)
    {
        $delivery = new Delivery($delivery_id);
        if (!$delivery['id']) {
            throw new ApiException(t('Доставка не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $address = new Address($address_id);
        if (!$address['id']) {
            throw new ApiException(t('Адрес не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $type = $delivery->getTypeObject();
        if ($type->hasPvz()) {
            $pvz_list = $type->getPvzByAddress($address);
            $response = [];
            foreach($pvz_list as $pvz) {
                $response[] = $pvz->asArray();
            }

            return [
                'response' => [
                    'list' => $response
                ]
            ];
        } else {
            throw new ApiException(t('Доставка не предусматривает пункты выдачи'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
    }
}