<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Shipment;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Shop\Model\Orm\Shipment;
use ExternalApi\Model\Exception as ApiException;

/**
 * Метод API, удаляющий отгрузку
 */
class Delete extends AbstractAuthorizedMethod
{
    const RIGHT_DELETE = 'delete';

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
            self::RIGHT_DELETE => t('Удаление отгрузки')
        ];
    }

    /**
     * Удаляет документ "отгрузка"
     *
     * @param string $token Авторизационный токен
     * @param integer $shipment_id ID отгрузки
     *
     * @example GET /api/methods/shipment.delete?token=311211047ab5474dd67ef88345313a6e479bf616&shipment_id=24
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     *  }
     * </pre>
     *
     * @return array
     */
    public function process($token, $shipment_id)
    {
        $shipment = new Shipment($shipment_id);
        if (!$shipment['id']) {
            throw new APIException(t('Отгрузка не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if ($shipment->delete()) {
            return [
                'response' => [
                    'success' => true
                ]
            ];
        } else {
            throw new ApiException(t('Не удалось удалить отгрузку. %0', [$shipment->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }
    }
}