<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Transaction;

use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils as ApiUtils;
use RS\Helper\CustomView;
use Shop\Model\CashRegisterApi;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;
use Shop\Model\TransactionApi;
use Users\Model\Orm\User;

/**
 * Метод для пополнения/списания баланса пользователя
 */
class ChangeBalance extends AbstractAuthorizedMethod
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
            self::RIGHT_ADD_FUNDS => 'Пополнение/списание баланса пользователя',
        ];
    }

    /**
     * Возвращает массив данных, подготовленный для внешнего API
     *
     * @param Transaction $object
     * @return array
     */
    public function getResult($object)
    {
        Get::appendDynamicProperties($object);
        Get::appendDynamicValues($object);

        return ApiUtils::extractOrm($object);
    }

    /**
     * Проверяет значение переданной суммы
     *
     * @param $amount
     * @return array
     * @throws ApiException
     */
    private function checkAmount($amount)
    {
        if (!is_numeric($amount) || $amount == 0) {
            throw new ApiException(t('Не указана сумма'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $number = (float)$amount;
        $absoluteValue = abs($amount);
        $writeoff = ($number < 0);

        return [$writeoff, $absoluteValue];
    }

    /**
     * Создает транзакцию на пополнение/списание средств пользователя
     *
     * @param string $token Автороизационный токен
     * @param float $user_id ИД пользователя
     * @param float $amount Сумма пополнения (списания). Если передано отрицательное число, то списание, если положительное то пополнение
     * @param string $reason Причина
     * @param integer $force_create_receipt Создать чек
     * @param string $receipt_payment_subject Признак предмета товара для чека (возможные значения в ключах CashRegisterApi::getStaticPaymentSubjects())
     *
     * @example GET /api/methods/transaction.сhangeBalance?token=04c763cf2f9b171b0f2acde756241d849d67087d&user_id=1&amount=-100reason=Списание
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "transaction": {
     *              "id": "1",
     *              "dateof": "2024-05-03 11:39:50",
     *              "user_id": "1",
     *              "cost": "-100",
     *              "comission": "349.97",
     *              "payment": "0",
     *              "reason": "Списание",
     *              "status": "success",
     *              "receipt": "refund_success",
     *              "partner_id": "0",
     *              "action": null,
     *              "receipts": [
     *                  {
     *                      "id": "4",
     *                      "sign": "111...",
     *                      "uniq_id": "aaaa...",
     *                      "type": "sell_refund",
     *                      "provider": "atolonline",
     *                      "dateof": "2024-05-03 14:56:05",
     *                      "transaction_id": "5",
     *                      "total": "9999.00",
     *                      "status": "success",
     *                  },
     *                  ...
     *              ]
     *          },
     *          "status": {
     *              "success": "Успешно",
     *              "fail": "Ошибка",
     *              "wait": "Ожидание ответа провайдера"
     *          },
     *          "type": {
     *              "sell": "Чек продажи",
     *              "sell_refund": "Чек возврата",
     *              "sell_correction": "Чек корректировки"
     *          },
     *          "user": {
     *              "id": "37",
     *              "name": "Иван",
     *              "surname": "Иванов",
     *              "midname": Иванович,
     *              "e_mail": "user@example.com",
     *              "login": "userexample",
     *              "phone": "+70000000000",
     *              ...
     *          }
     *      }
     * }
     * </pre>
     *
     * @return array Возвращает объект транзакции и все связанные с ним объекты из справочников
     */
    public function process($token, $user_id, $amount, $reason, $force_create_receipt = 0, $receipt_payment_subject = null)
    {
        if ($user_id) {
            $user = new User($user_id);
        }

        if (!isset($user) || !$user['id']) {
            throw new ApiException(t('Пользователь не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }else {
            list($writeoff, $amount_value) = $this->checkAmount($amount);

            $transaction_api = new TransactionApi();
            $transaction = $transaction_api->addFunds($user['id'], $amount_value, $writeoff, $reason);
            if ($transaction['id']) {
                if ($writeoff && $force_create_receipt) {
                    if (array_key_exists($receipt_payment_subject, CashRegisterApi::getStaticPaymentSubjects())) {
                        $transaction['force_create_receipt'] = $force_create_receipt;
                        $transaction['receipt_payment_subject'] = $receipt_payment_subject;
                        $result = $transaction_api->createReceipt($transaction);
                        if ($result !== true) {
                            throw new ApiException($result, ApiException::ERROR_INSIDE);
                        }
                    }else {
                        throw new ApiException(t('Неверное значение признака предмета товара для чека'), ApiException::ERROR_WRONG_PARAM_VALUE);
                    }
                }

                $response = [
                    'response' => [
                        'transaction' => $this->getResult($transaction)
                    ]
                ];

                $currency = CurrencyApi::getCurrentCurrency();

                $response['response']['transaction']['receipts_data']['status'] = Receipt::handbookStatuses();
                $response['response']['transaction']['cost_formatted']
                    = CustomView::cost($response['response']['transaction']['cost'], $currency['stitle']);
                $response['response']['transaction']['receipts_data']['type'] = Receipt::handbookType();
                $response['response']['user'] = ApiUtils::extractOrm(new User($transaction['user_id']));
                $response['response']['status'] = Transaction::handbookStatus();
                $response['response']['receipt'] = Transaction::handbookReceipt();
                return $response;
            } else {
                throw new ApiException($transaction->getErrorsStr(), ApiException::ERROR_INSIDE);
            }
        }
    }
}