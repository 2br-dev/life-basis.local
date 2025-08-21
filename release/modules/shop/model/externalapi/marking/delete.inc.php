<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Marking;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\OrderItemUIT;

/**
 * Удаляет маркировку из заказа
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
            self::RIGHT_DELETE => t('Удаление маркировки')
        ];
    }

    /**
     * Удаляет не связанную с отгрузкой маркировку
     *
     * @param string $token Авторизационный токен
     * @param integer $uit_id ID маркировки
     *
     * @example GET /api/methods/marking.delete?token=311211047ab5474dd67ef88345313a6e479bf616&uit_id=24
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     *
     * @return array
     */
    public function process($token, $uit_id)
    {
        $uit = new OrderItemUIT($uit_id);
        if (!$uit['id']) {
            throw new ApiException(t('Маркировка не найдена'));
        }

        if ($uit->isInShipment()) {
            throw new ApiException(t('Маркировку нельзя удалить, так как она присутствует в отгрузке'));
        }

        if ($uit->delete()) {
            return [
                'response' => [
                    'success' => true
                ]
            ];
        } else {
            throw new ApiException(t('Не удалось удалить маркировку. %0', [$uit->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }
    }
}