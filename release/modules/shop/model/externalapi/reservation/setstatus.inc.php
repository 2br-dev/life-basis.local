<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Reservation;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use RS\Exception;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Reservation;

/**
 * Обновляет статус предзаказа
 */
class SetStatus extends AbstractAuthorizedMethod
{
    const RIGHT_UPDATE = 1;

    private $statuses;

    function __construct()
    {
        parent::__construct();
        $this->statuses = array_keys(Reservation::getStatusTitles());
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
            self::RIGHT_UPDATE => t('Изменение статуса'),
        ];
    }

    /**
     * Обновляет статус предзаказа
     *
     * @param string $token Авторизационный токен
     * @param integer $reservation_id ID покупки в 1 клик
     * @param string $status статус (возможные значения в ключах Reservation::getStatusTitles())
     *
     * @return array Возвращает предзаказ
     * @example POST /api/methods/reservation.setStatus?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&reservation_id=1&status=open
     *
     * Ответ:
     *  <pre>
     *  {
     *      "response": {
     *          "success": true,
     *          "reservation": {
     *              "id": "90",
     *              "product_id": "78688",
     *              "product_barcode": "a078688-4",
     *              "product_title": "Многомерные комплектации",
     *              "offer_id": "10708",
     *              "currency": "RUB",
     *              "multioffer": [
     *                  {
     *                      "title": "Форм-фактор",
     *                      "value": "Нетбук"
     *                  },
     *                  {
     *                      "title": "test",
     *                      "value": "2"
     *                  }
     *              ],
     *              "amount": 1,
     *              "phone": "+79280000001",
     *              "email": "demo@example.com",
     *              "is_notify": "1",
     *              "dateof": "2024-08-29 11:49:59",
     *              "user_id": "2",
     *              "status": "open",
     *              "comment": null,
     *              "partner_id": "0",
     *              "offer_title": "Нетбук, 2",
     *              "status_color": "#ffa545",
     *              "unit": "шт.",
     *              "single_cost": 0,
     *              "single_cost_formatted": "0 ₽",
     *              "total_cost": 0,
     *              "total_cost_formatted": "0 ₽",
     *              "image": {
     *                  "id": null,
     *                  "title": null,
     *                  "original_url": "https://full.readyscript.local/resource/img/photostub/nophoto.jpg",
     *                  "big_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_1000x1000/nophoto_81b6f2b0.jpg",
     *                  "middle_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_600x600/nophoto_3443afef.jpg",
     *                  "small_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_300x300/nophoto_62e5d4e1.jpg",
     *                  "micro_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_100x100/nophoto_9a394c67.jpg",
     *                  "nano_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_50x50/nophoto_e7484ef.jpg"
     *              }
     *          }
     *      }
     *  }
     *  </pre>
     *
     */
    public function process($token, $reservation_id, $status)
    {
        if (!in_array($status, $this->statuses)) {
            throw new ApiException(t('Неверное значение параметра status'), ApiException::ERROR_WRONG_PARAMS);
        }
        $reservation = new Reservation();
        if ($reservation->load($reservation_id)) {
            Get::appendReservationProperties($reservation);
            try {
                $reservation['status'] = $status;
                $reservation->update();
                Get::appendReservationDynamicValues($reservation);
                return [
                    'response' => [
                        'success' => true,
                        'reservation' => Utils::extractOrm($reservation)
                    ]
                ];
            } catch(Exception $e) {
                throw new ApiException($e->getMessage(), ApiException::ERROR_WRONG_PARAM_VALUE);
            }

        }else {
            throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }
    }
}