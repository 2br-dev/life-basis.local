<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Transaction;
use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractGet;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils as ApiUtils;
use RS\Helper\CustomView;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;
use RS\Orm\Type;
use Shop\Model\ReceiptApi;
use Users\Model\Orm\User;

/**
* Загружает объект транзакции
*/
class Get extends AbstractGet
{
    const RIGHT_LOAD = 1;
    const RIGHT_SELF_LOAD = 2;

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
            self::RIGHT_LOAD => t('Загрузка всех транзакций'),
            self::RIGHT_SELF_LOAD => t('Загрузка транзакций текущего пользователя'),
        ];
    }

    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new Transaction();
    }

    /**
     * Добавляет к транзакции информацию по чекам
     *
     * @param $transaction
     * @return mixed
     */
    public static function addReceiptsData($transaction)
    {
        $receipt_api = new ReceiptApi();
        $receipt_api->getElement()->getPropertyIterator()->append([
            'total_formatted' => new Type\MixedType([
                'appVisible' => true
            ]),
            'status_color' => new Type\MixedType([
                'appVisible' => true
            ])
        ]);

        $receipt_api->setFilter('transaction_id', $transaction->id);
        $currency = CurrencyApi::getCurrentCurrency();
        $receipts = $receipt_api->getList();
        foreach ($receipts as $receipt) {
            $receipt['total_formatted'] = CustomView::cost($receipt['total'], $currency['stitle']);
            $receipt['status_color'] = $receipt->getStatusColor();
        }
        return ApiUtils::extractOrmList($receipts);
    }

    /**
     * Добавляет к объекту транзакции поля, которые необходимы только для внешнего API
     *
     * @param Transaction $transaction
     * @return void
     */
    public static function appendDynamicProperties(Transaction $transaction)
    {
        $transaction->getPropertyIterator()->append([
            'action' => new Type\ArrayList([
                'appVisible' => true
            ]),
            'receipts' => new Type\ArrayList([
                'appVisible' => true
            ]),
            'receipts_data' => new Type\ArrayList([
                'appVisible' => true
            ]),
            'status_color' => new Type\Varchar([
                'appVisible' => true
            ])
        ]);
    }

    /**
     * Добавляет сведения, необходимые для API
     *
     * @param Transaction $transaction
     * @return Transaction
     */
    public static function appendDynamicValues(Transaction $transaction)
    {
        $transaction['receipts'] = self::addReceiptsData($transaction);
        $transaction['action'] = \Shop\Model\ApiUtils::getActionInfo($transaction);
        $transaction['status_color'] = $transaction->getStatusColor();
        return $transaction;
    }

    /**
     * Возвращает массив данных, подготовленный для внешнего API
     *
     * @param Transaction $object
     * @return array
     */
    public function getResult($object)
    {
        self::appendDynamicProperties($object);
        self::appendDynamicValues($object);

        return ApiUtils::extractOrm($object);
    }

    /**
     * Загружает объект транзакции
     *
     * @param string $token Авторизационный токен
     * @param integer $transaction_id ID транзакции
     *
     * @example GET /api/methods/transaction.get?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&transaction_id=5
     *
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
     * @return array Возвращает объект транзакции и все связанные с ним объекты из справочников
     */
    protected function process($token, $transaction_id)
    {
        $this->object = $this->getOrmObject();
        if ($this->object->load($transaction_id)) {

            if ($this->token['user_id'] != $this->object['user_id'] &&
                ($error = $this->checkAccessError(self::RIGHT_LOAD))) {
                throw new ApiException($error, ApiException::ERROR_METHOD_ACCESS_DENIED);
            }

            $response = [
                'response' => [
                    $this->getObjectSectionName() => $this->getResult($this->object)
                ]
            ];

            $currency = CurrencyApi::getCurrentCurrency();

            $response['response'][$this->getObjectSectionName()]['receipts_data']['status'] = Receipt::handbookStatuses();
            $response['response'][$this->getObjectSectionName()]['cost_formatted']
                = CustomView::cost($response['response'][$this->getObjectSectionName()]['cost'], $currency['stitle']);
            $response['response'][$this->getObjectSectionName()]['receipts_data']['type'] = Receipt::handbookType();
            $response['response']['user'] = ApiUtils::extractOrm(new User($this->object['user_id']));
            $response['response']['payment'] = ApiUtils::extractOrm(new Payment($this->object['payment']));
            $response['response']['status'] = Transaction::handbookStatus();
            $response['response']['receipt'] = Transaction::handbookReceipt();

            return $response;
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}
