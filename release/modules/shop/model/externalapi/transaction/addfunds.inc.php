<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Transaction;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use RS\Router\Manager;
use Shop\Config\File;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Payment;
use Shop\Model\TransactionApi;

/**
 * Метод для пополнения баланса пользователя
 */
class AddFunds extends AbstractAuthorizedMethod
{
    const RIGHT_ADD_FUNDS = 1;
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
            self::RIGHT_ADD_FUNDS => 'Пополнение баланса своего л/с',
        ];
    }

    /**
     * Метод создает транзакцию и возвращает URL на платежную страницу для пополнения баланса
     * ---
     * Метод поддерживает только online способы оплаты.
     *
     * @param string $token Автороизационный токен
     * @param float $amount Сумма пополнения баланса
     * @param integer $payment_id ID способа оплаты
     *
     * @example GET /api/methods/transaction.addFunds?token=04c763cf2f9b171b0f2acde756241d849d67087d&payment_id=5&amount=100
     *
     * Ответ:
     * <pre>
     * {
     *       "response": {
     *           "success": true,
     *           "pay_url": "https://example.com/onlinepay/payTransaction/?transaction=676"
     *       }
     * }
     * </pre>
     *
     * @return array
     */
    public function process($token, $amount, $payment_id)
    {
        $config = File::config();
        if (!$config['use_personal_account']) {
            throw new ApiException(t('Пополнение баланса отключено в настройках'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }

        if ($amount <= 0) {
            throw new ApiException(t('Укажите положительную сумму пополнения баланса'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if (!$payment_id) {
            throw new ApiException(t('Укажите способ оплаты'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $payment = new Payment($payment_id);
        if (!$payment['id']) {
            throw new ApiException(t('Способ оплаты не найден'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if (!in_array($payment['target'], ['all', 'refill'])) {
            throw new ApiException(t('Способ оплаты не поддерживает пополнение баланса'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if (!$payment->getTypeObject()->canOnlinePay()) {
            throw new ApiException(t('Способ оплаты не поддерживает online оплату'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $transaction_api = new TransactionApi();
        $transaction = $transaction_api->createTransactionForAddFunds($this->token->user_id, $payment['id'], $amount);
        if ($transaction['id']) {
            $router = Manager::obj();
            $pay_url = $router->getUrl('shop-front-onlinepay', ['Act' => 'payTransaction',  'transaction' => $transaction['id']], true);

            return [
                'response' => [
                    'success' => true,
                    'pay_url' => $pay_url
                ]
            ];
        } else {
            throw new ApiException($transaction->getErrorsStr(), ApiException::ERROR_INSIDE);
        }
    }
}