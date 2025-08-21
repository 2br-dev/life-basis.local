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
use \ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils as ApiUtils;
use RS\Config\Loader;
use RS\Helper\CustomView;
use RS\Http\Request;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;
use Shop\Model\ReceiptApi;
use RS\Orm\Type;
use Shop\Model\TransactionApi;
use Users\Model\Orm\User;

/**
 * Выполняет одно действие с транзакцией
 */
class Action extends AbstractAuthorizedMethod
{
    const RIGHT_RUN = 1;

    private $actions = [
        'setTransactionSuccess',
        'sendReceipt',
        'sendRefundReceipt'
    ];

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
            self::RIGHT_RUN => t('Выполнение одного действия с транзакцией')
        ];
    }

    /**
     * Добавляет к транзакции информацию по чекам
     *
     * @param $dao
     * @return mixed
     */
    public function addReceiptsData($dao)
    {
        $receipt_api = new ReceiptApi();
        $receipt_api->getElement()->getPropertyIterator()->append([
            'total_formatted' => new Type\Varchar([
                'appVisible' => true
            ]),
        ]);

        $receipt_api->setFilter('transaction_id', $dao->id);
        $currency = CurrencyApi::getCurrentCurrency();
        $receipts = $receipt_api->getList();
        foreach ($receipts as $receipt) {
            $receipt['total_formatted'] = CustomView::cost($receipt['total'], $currency['stitle']);
        }
        $dao['receipts'] = ApiUtils::extractOrmList($receipts);
        return $dao;
    }

    /**
     * Возвращает объект
     */
    public function getResult($dao)
    {
        Get::appendDynamicProperties($dao);
        Get::appendDynamicValues($dao);

        $result = [
            'response' => [
                'transaction' => ApiUtils::extractOrm($dao)
            ]
        ];

        $currency = CurrencyApi::getCurrentCurrency();

        $result['response']['transaction']['cost_formatted'] = CustomView::cost($result['response']['transaction']['cost'], $currency['stitle']);
        $result['response']['transaction']['receipts_data']['status'] = Receipt::handbookStatuses();
        $result['response']['transaction']['receipts_data']['type'] = Receipt::handbookType();
        $result['response']['user'] = ApiUtils::extractOrm(new User($dao['user_id']));
        $result['response']['payment'] = ApiUtils::extractOrm(new Payment($dao['payment']));
        $result['response']['status'] = Transaction::handbookStatus();
        $result['response']['receipt'] = Transaction::handbookReceipt();

        return $result;
    }

    /**
     * Выполняет одно действие с транзакцией (начислить средства, оплатить заказ, выбить чек, сделать чек возврата)
     *
     * @param string $token Авторизационный token
     * @param integer $transaction_id ID транзакции
     * @param string $action действие, возможные значения
     *  <b>setTransactionSuccess</b> - начислить средства, оплатить заказ
     *  <b>sendReceipt</b> - выбить чек
     *  <b>sendRefundReceipt</b> - сделать чек возврата
     *
     * @return array Возвращает объект транзакции и все связанные с ним объекты из справочников или ошибку
     * @example GET /api/methods/transaction.action?token=894b9df5ebf40531d560235d7379a8cff50f930f&transaction_id=5&action=setTransactionSuccess
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "transaction": {
     *              "id": "5",
     *              "dateof": "2024-05-03 11:39:50",
     *              "user_id": "37",
     *              "cost": "9999.00",
     *              "comission": "349.97",
     *              "payment": "4",
     *              "reason": "Пополнение баланса лицевого счета",
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
     */
    public function process($token, $transaction_id, $action)
    {
        $transaction = new Transaction($transaction_id);
        $config = Loader::byModule($this);
        $transaction_api = new TransactionApi();

        if (!$transaction['id']) {
            throw new ApiException(t('Транзакция с указанным transaction_id не найдена'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        if (!in_array($action, $this->actions)) {
            throw new ApiException(t('Неверное значение параметра action'), ApiException::ERROR_WRONG_PARAMS);
        }

        switch($action) {
            case 'setTransactionSuccess':
                if ($transaction->getPayment()->getTypeObject()->canOnlinePay()) {
                    throw new ApiException(t('Способ оплаты поддерживает только автоматическое проведение'), ApiException::ERROR_WRITE_ERROR);
                }
                try {
                    $transaction->onResult(Request::commonInstance());
                } catch (\Exception $e) {
                    throw new ApiException(t($e->getMessage()), ApiException::ERROR_WRITE_ERROR);
                }

                return $this->getResult($transaction);
            case 'sendReceipt':
                if (!$config->cashregister_class) {
                    throw new ApiException(t('Не назначен провайдер'), ApiException::ERROR_WRITE_ERROR);
                }
                try {
                    if (($result = $transaction_api->createReceipt($transaction)) === true) {
                        return $this->getResult($transaction);
                    } else {
                        throw new ApiException(t($result), ApiException::ERROR_WRITE_ERROR);
                    }
                } catch (\Exception $e) {
                    throw new ApiException(t($e->getMessage()), ApiException::ERROR_WRITE_ERROR);
                }
            case 'sendRefundReceipt':
                if (!$config->cashregister_class) {
                    throw new ApiException(t('Не назначен провайдер'), ApiException::ERROR_WRITE_ERROR);
                }
                try {
                    if (($result = $transaction_api->createReceipt($transaction, \Shop\Model\CashRegisterType\AbstractType::OPERATION_SELL_REFUND)) === true) {
                        return $this->getResult($transaction);
                    } else {
                        throw new ApiException(t($result), ApiException::ERROR_WRITE_ERROR);
                    }
                } catch (\Exception $e) {
                    throw new ApiException(t($result), ApiException::ERROR_WRITE_ERROR);
                }
        }
    }

}