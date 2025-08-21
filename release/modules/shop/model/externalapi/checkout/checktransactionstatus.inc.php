<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use Catalog\Model\WareHouseApi;
use ExternalApi\Model\Validator\ValidateArray;
use Main\Model\StatisticEvents;
use \ExternalApi\Model\Exception as ApiException;
use RS\Application\Auth;
use Shop\Model\AddressApi;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;
use Shop\Model\PaymentApi;

/**
* Получение статуса транзакции
*/
class CheckTransactionStatus extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    /**
     * Получает статус транзакции
     * @param string $transaction_id ID транзакции
     * @param string $token Авторизационный токен
     *
     * @example GET /api/methods/checkout.checkTransactionStatus?transaction_id=1
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "errors": [],
     *          "status": "success"
     *      }
     * }
     * </pre>
     * @return mixed Возвращает статус транзакции
     */
    protected function process($transaction_id, $token = null)
    {
        $response['response']['errors'] = [];

        $transaction = new Transaction($transaction_id);
        if ($transaction['id']) {
            $payment_type = $transaction->getPayment()->getTypeObject();
            if ($transaction['status'] == Transaction::STATUS_NEW && $payment_type->canOnlinePay()) {
                $payment_type->checkPaymentStatus($transaction);
            }

            $response['response']['status'] = $transaction['status'];
        } else {
            $response['response']['status'] = false;
        }

        return $response;
    }
}
