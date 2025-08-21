<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Transaction;

use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractGetList;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils as ApiUtils;
use RS\Helper\CustomView;
use Shop\Model\Orm\Transaction;
use Shop\Model\PaymentApi;
use Shop\Model\ReceiptApi;
use Shop\Model\TransactionApi;
use RS\Orm\Type;
use Users\Model\Api as UserApi;
use Users\Model\Orm\User;

/**
* Возвращает список транзакций
*/
class GetList extends AbstractGetList
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
            self::RIGHT_LOAD => t('Загрузка данных'),
            self::RIGHT_SELF_LOAD => t('Загрузка данных для своего лицевого счета'),
        ];
    }

    /**
     * Возвращает объект выборки объектов
     *
     * @return TransactionApi
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new TransactionApi();
        }
        return $this->dao;
    }

    /**
     * Возвращает возможный ключи для фильтров
     *
     * @return [
     *   'поле' => [
     *       'type' => 'тип значения'
     *   ]
     * ]
     */
    public function getAllowableFilterKeys()
    {
        return [
            'query' => [
                'title' => t('Универсальный поиск по различным полям. В текущее время ищет по номеру транзакции, назначению транзакции, электронной почте пользователя, фамилии и имени пользователя.'),
                'type' => 'string',
                'func' => 'fullSearch'
            ],
            'payment' => [
                'func' => self::FILTER_TYPE_IN,
                'type' => 'integer',
            ],
            'status' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',
                'values' => [
                    Transaction::STATUS_NEW,
                    Transaction::STATUS_HOLD,
                    Transaction::STATUS_SUCCESS,
                    Transaction::STATUS_FAIL
                ]
            ],
            'receipt' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',
                'values' => [
                    Transaction::NO_RECEIPT,
                    Transaction::RECEIPT_IN_PROGRESS,
                    Transaction::RECEIPT_SUCCESS,
                    Transaction::RECEIPT_REFUND_SUCCESS,
                    Transaction::RECEIPT_FAIL
                ]
            ],
        ];
    }

    /**
     * Возвращает готовое условие для установки фильтра. (Тип фильтра - частичное совпадение %like%)
     *
     * @param string $key - поле фильтрации
     * @param mixed $value - значение фильтра
     * @param array $filters - весь список фильтров
     * @param array $filter_settings - параметры фильтра,
     * @return array
     */
    protected function makeFilterFullSearch($key, $value, $filters, $filter_settings)
    {
        $dao = $this->getDaoObject();
        $q = $dao->queryObj();
        $q->select('A.*');
        $q->leftjoin(new User(), '`A`.`user_id`=`U`.`id`', 'U');
        $q->where("(`A`.`id` = '#term' OR `A`.`reason` like '%#term%' OR `U`.`name` like '%#term%' OR `U`.`surname` like '%#term%' OR `U`.`e_mail` like '%#term%')", [
            'term' => $value
        ]);
        $q->groupby('A.id');

        return [];
    }

    /**
     * Устанавливает фильтр для выборки
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param array $filter
     * @return void
     */
    public function setFilter($dao, $filter)
    {
        if ($this->hasRights(self::RIGHT_SELF_LOAD)) {
            $dao->setPersonalAccountTransactionsFilter($this->token['user_id']);
            $dao->setFilter('status', 'success');
        }

        $dao->setFilter($this->makeFilter($filter));
    }

    /**
     * Устанавливает сортировку для выборки
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param string $order - предложенная сортировка
     * @throws ApiException
     */
    public function setOrder($dao, $order)
    {
        $dao->setOrder('A.'.$this->makeOrder($order));
    }

    /**
     * Добавляет к транзакции информацию по чекам
     *
     * @param $transaction
     * @return mixed
     */
    public function addReceiptsData($transaction)
    {
        $receipt_api = new ReceiptApi();
        $receipt_api->getElement()->getPropertyIterator()->append([
            'total_formatted' => new Type\Varchar([
                'appVisible' => true
            ]),
        ]);

        $receipt_api->setFilter('transaction_id', $transaction->id);
        $currency = CurrencyApi::getCurrentCurrency();
        $receipts = $receipt_api->getList();
        foreach ($receipts as $receipt) {
            $receipt['total_formatted'] = CustomView::cost($receipt['total'], $currency['stitle']);
        }
        return ApiUtils::extractOrmList($receipts);
    }

    /**
     * Возвращает список объектов
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getResultList($dao, $page, $pageSize)
    {
        Get::appendDynamicProperties($dao->getElement());

        $list = $dao->getList($page, $pageSize);
        foreach ($list as $transaction) {
            Get::appendDynamicValues($transaction);
        }

        return ApiUtils::extractOrmList($list);
    }

    /**
     * Возвращает возможные значения для сортировки
     *
     * @return array
     */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'cost', 'cost desc'];
    }

    /**
     * Выполняет запрос на выборку транзакций
     *
     * @param string $token Авторизационный token
     * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
     * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
     * @param integer $page Номер страницы, начинается с 1
     * @param mixed $pageSize Размер страницы
     *
     * @return array Возвращает список транзакций и связанные с транзакциями сведения.
     * @example GET /api/methods/transaction.getlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&page=1&pageSize=20
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "summary": {
     *             "page": "1",
     *             "pageSize": "20",
     *             "total": "1"
     *         },
     *         "list": [
     *         {
     *              "id": "2",
     *              "dateof": "2024-05-07 10:20:42",
     *              "user_id": "1",
     *              "cost": "600.00",
     *              "comission": null,
     *              "payment": "3",
     *              "reason": "Оплата заказа №109",
     *              "status": "success",
     *              "receipt": "no_receipt",
     *              "partner_id": "0",
     *              "action": {
     *                  "action": "sendReceipt",
     *                  "confirm_text": "Вы действительно желаете выбить чек по данной операции?",
     *                  "title": "Выбить чек"
     *              },
     *              "cost_formatted": "600 р."
     *         },
     *         {
     *              "id": "1",
     *              "dateof": "2024-05-07 10:19:29",
     *              "user_id": "2",
     *              "cost": "34600.00",
     *              "comission": null,
     *              "payment": "3",
     *              "reason": "Оплата заказа №109",
     *              "status": "new",
     *              "receipt": "no_receipt",
     *              "partner_id": "0",
     *              "action": {
     *                  "action": false,
     *                  "confirm_text": null,
     *                  "title": null
     *              },
     *              "cost_formatted": "34 600 р."
     *         },
     *         ...
     *         ],
     *         "user": {
     *             "2": {
     *                 "id": "2",
     *                 "name": "Артем",
     *                 "surname": "Иванов",
     *                 "midname": "Петрович",
     *                 "e_mail": "mail@readyscript.ru",
     *                 "login": "demo@example.com",
     *                 "phone": "+700000000000",
     *                 "sex": "",
     *                 "subscribe_on": "0",
     *                 "dateofreg": "0000-00-00 00:00:00",
     *                 "ban_expire": null,
     *                 "last_visit": "2016-09-19 16:02:09",
     *                 "is_company": "1",
     *                 "company": "ООО Ромашка",
     *                 "company_inn": "1234567890",
     *                 "data": {
     *                     "passport": "00000012233"
     *                 },
     *                 "passport": "серия 03 06, номер 123456, выдан УВД Западного округа г. Краснодар, 04.03.2006",
     *                 "company_kpp": "0987654321",
     *                 "company_ogrn": "1234567890",
     *                 "company_v_lice": "директора Сидорова Семена Петровича",
     *                 "company_deistvuet": "устава",
     *                 "company_bank": "ОАО УРАЛБАНК",
     *                 "company_bank_bik": "1234567890",
     *                 "company_bank_ks": "10293847560192837465",
     *                 "company_rs": "19283746510293847560",
     *                 "company_address": "350089, г. Краснодар, ул. Чекистов, 12",
     *                 "company_post_address": "350089, г. Краснодар, ул. Чекистов, 15",
     *                 "company_director_post": "директор",
     *                 "company_director_fio": "Сидоров С.П.",
     *                 "user_cost": null
     *             }
     *         },
     *         "user": {
     *             "1": {
     *                 "id": "1",
     *                  "name": "Иван",
     *                  "surname": "Иванов",
     *                  "midname": "Иванович",
     *                  "e_mail": "example@mail.com",
     *                  "login": "example@mail.com",
     *                  "phone": "+79990000000",
     *             },
     *             ...
     *         },
     *         "payment": {
     *             "1": {
     *                  "id": "1",
     *                  "title": "Оплата с лицевого счета",
     *                  "description": "На счете должно быть достаточно средств",
     *                  "public": "1",
     *                  "class": "personalaccount",
     *                  ...
     *             },
     *             ...
     *         }
     *     }
     * }
     * </pre>
     *
     */
    protected function process($token, $filter = [], $sort = 'id desc', $page = "1", $pageSize = "20")
    {
        $result = parent::process($token, $filter, $sort, $page, $pageSize);
        $currency = CurrencyApi::getCurrentCurrency();
        $users_ids = [];
        $payment_ids = [];

        if ($result['response']['list']) {
            foreach($result['response']['list'] as $key => $transaction) {
                $users_ids[$transaction['user_id']] = $transaction['user_id'];
                $payment_ids[$transaction['payment']] = $transaction['payment'];
                $result['response']['list'][$key]['cost_formatted'] = CustomView::cost($result['response']['list'][$key]['cost'], $currency['stitle']);
            }

            $result['response']['status'] = Transaction::handbookStatus();
            $result['response']['receipt'] = Transaction::handbookReceipt();
        }

        if ($users_ids) {
            $user_api = new UserApi();
            $user_api->setFilter('id', $users_ids, 'in');
            $users = $user_api->getAssocList('id');
            $result['response']['user'] = ApiUtils::extractOrmList($users, 'id');
        }

        if ($payment_ids) {
            $payment_api = new PaymentApi();
            $payment_api->setFilter('id', $payment_ids, 'in');
            $payments = $payment_api->getAssocList('id');
            $result['response']['payment'] = ApiUtils::extractOrmList($payments, 'id');
        }

        return $result;
    }
}