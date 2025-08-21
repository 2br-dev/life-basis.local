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
use RS\Exception;
use Shop\Model\Orm\Order;

/**
 * Метод API, оплачивает заказ с лицевого счета
 */
class PayWithPersonalAccount extends AbstractAuthorizedMethod
{
    const RIGHT_PAY = 1;
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
            self::RIGHT_PAY => t('Оплата заказа с лицевого счета'),
        ];
    }

    /**
     * Оплачивает заказ с лицевого счета
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     *
     * @example POST /api/methods/order.payWithPersonalAccount?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=159
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true,
     *         "message": [
     *              "text": "Успешно списано 10.00 с лицевого счета пользователя"
     *          ]
     *     }
     *  }
     * </pre>
     *
     * @return array
     */
    public function process($token, $order_id)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        try {
            $result =  $order->getPayment()->getTypeObject()->actionOrderQuery($order, ['operation' => 'orderpay']);
            if ($result) {
                return [
                    'response' => [
                        'success' => $result['success'],
                        'message' => $result['messages']
                    ]
                ];
            }else {
                throw new ApiException(t('Не удалось оплатить заказ'), ApiException::ERROR_INSIDE);
            }
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRITE_ERROR);
        }
    }
}