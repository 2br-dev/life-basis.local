<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Reservation;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Reservation;

class Delete extends AbstractAuthorizedMethod
{
    const RIGHT_DELETE = 1;

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
            self::RIGHT_DELETE => t('Удаление предзаказа')
        ];
    }

    /**
     * Удаляет одну покупку в 1 клик
     *
     * @param string $token Авторизационный токен
     * @param integer $reservation_id ID покупки в 1 клик
     *
     * @return array
     * @example POST api/methods/reservation.delete?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&reservation_id=1
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true
            }
        }
     * </pre>
     */
    public function process($token, $reservation_id)
    {
        $reservation = new Reservation($reservation_id);
        if (!$reservation['id']) {
            throw new ApiException(t('Предзаказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if (!$reservation->delete()) {
            throw new ApiException(t('Ошибка удаления: %0', [$reservation->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }

        return [
            'response' => [
                'success' => true
            ]
        ];
    }
}