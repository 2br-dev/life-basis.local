<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Reservation;
use Shop\Model\ReservationApi;

/**
 * Метод API, создает заказ из предварительного заказа
 */
class CreateByReservation extends AbstractAuthorizedMethod
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
            self::RIGHT_CREATE => t('Создание заказа из предварительного заказа'),
        ];
    }

    /**
     * Создает заказ из предварительного заказа
     *
     * @param string $token Авторизационный токен
     * @param integer $reservation_id ID предварительного заказа
     *
     * @example POST /api/methods/order.createbyreservation?token=311211047ab5474dd67ef88345313a6e479bf616&reservation_id=87
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true,
     *         "order_id": 1528
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает ID созданного заказа
     */
    public function process($token, $reservation_id)
    {
        $reservation = Reservation::loadByWhere([
            'id' => $reservation_id
        ]);

        if (!$reservation['id']) {
            throw new ApiException(t('Предварительный заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $reservation_api = new ReservationApi();
        $order = $reservation_api->createOrderFromReservation($reservation);

        return [
            'response' => [
                'success' => true,
                'order_id' => $order['id']
            ]
        ];
    }
}