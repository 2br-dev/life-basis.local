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
use Shop\Model\PaymentType\InterfaceRecurringPayments;

/**
 * Выполняет действие со связанным рекуррентным платежом по заказу
 */
class DoPaymentAction extends AbstractAuthorizedMethod
{
    const RIGHT_RUN = 1;

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
            self::RIGHT_RUN => t('Право на выполнение действия')
        ];
    }

    /**
     * Выполняет действие со связанным платежом по заказу
     * ---
     * Если переданы параметры order_id и action (pay_with_saved_method), то выполняет действие со связанным рекуррентным платежом (создание транзакции на списание для выбранного метода платежа).
     * Если переданы параметры order_id, transaction_id, action, то выполняет действие с указанной транзакцией (завершение или отмена холдирования).
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     * @param string $action Тип действия. Допустимые значения:
     * Если не передан transaction_id:
     * <b>pay_with_saved_method</b> - списать средства с выбранного метода платежа
     *
     * Если передан transaction_id, то список действий зависит от способа оплаты и
     * возвращается в методе order.save в ключе order.last_transaction.actions
     *
     * @param integer $transaction_id ID транзакции (передается для выполнения действий: завершение холдирования, отмена холдирования)
     * @return array
     *
     * @example GET /api/methods/order.doPaymentAction?token=a0f2c8568a23d3b8b68d51762ba6c98b8b417fcf&order_id=1542&transaction_id=405&action=hold_capture
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true,
                "message": "Оплата успешно завершена"
            }
        }
     * </pre>
     */
    function process($token, $order_id, $action, $transaction_id = null)
    {
        $order = new Order($order_id);

        if (!$order['id']) {
            throw new ApiException(t('Заказ с номером %0 не найден', [$order_id]), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if ($transaction_id) {
            $result = $this->processTransactionAction($order, $transaction_id, $action);
        } else {
            $result = $this->processRecurringAction($order, $action);
        }

        return $result;
    }

    /**
     * Выполняет действие по рекуррентным платежам
     *
     * @param Order $order
     * @param string $action
     * @return array[]
     * @throws ApiException
     */
    private function processRecurringAction(Order $order, string $action)
    {
        $payment_type = $order->getPayment()->getTypeObject();

        if ($action != InterfaceRecurringPayments::RECURRING_ACTION_PAY_WITH_SAVED_METHOD) {
            throw new ApiException(t('Недопустимое значение в поле action(действие). Допускается значение: pay_with_saved_method'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if (!($payment_type instanceof InterfaceRecurringPayments)) {
            throw new ApiException(t('Способ оплаты не поддерживает рекуррентные платежи'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        try {
            $result = $payment_type->executeInterfaceRecurringPaymentsAction($order, $action);
            if ($result['view_type'] == 'message') {
                return [
                    'response' => [
                        'success' => true,
                        'message' => $result['message']
                    ]
                ];
            } else {
                //В приложении мы можем только показать сообщение после выполнения действия
                throw new ApiException(t('Способ оплаты вернул неподдерживаемый тип результата %0', [$result['view_type']]), ApiException::ERROR_INSIDE);
            }
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRITE_ERROR);
        }
    }

    /**
     * Выполняет действие с последней транзакцией на оплату заказа
     *
     * @param Order $order
     * @param integer $transaction_id
     * @param string $action
     * @return array
     */
    private function processTransactionAction(Order $order, $transaction_id, string $action)
    {
        $last_transaction = $order->getOrderTransaction();
        if ($last_transaction['id'] != $transaction_id) {
            throw new ApiException(t('Есть более новые транзакции, операция не может быть выполнена'), ApiException::ERROR_WRITE_ERROR);
        }

        try {
            $result_string = $last_transaction->getPayment()->getTypeObject()->executeTransactionAction($last_transaction, $action);
            return [
                'response' => [
                    'success' => true,
                    'message' => $result_string
                ]
            ];
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRITE_ERROR);
        }
    }
}