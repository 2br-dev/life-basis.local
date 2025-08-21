<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Payment;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\SavedPaymentMethod;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\SavedPaymentMethodApi;
use Shop\Model\Exception as ShopException;

/**
* Удаляет платежный метод
*/
class DeletePaymentMethod extends AbstractAuthorizedMethod
{
    const RIGHT_DELETE = 1;

    public
        /**
        * @var PaymentApi
        */
        $payment_api;
    
    function __construct()
    {
        parent::__construct();
        $this->payment_api = new PaymentApi();
    }

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
            self::RIGHT_DELETE => t('Удаление платежного метода.')
        ];
    }

    /**
     * Удаляет сохраненный способ платежа клиента.
     *
     * @param string $token Авторизационный токен
     * @param string $saved_method_id ID привязанного платежного метода
     * @param string $payment_id ID способа оплаты
     *
     * @example GET /api/methods/payment.setDefaultPaymentMethod?token=123123123123123123123&payment_id=1&saved_method_id=42
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "status": true,
     *          "payment_list": [
     *              {
     *                  "id": "1",
     *                  "title": "Картой онлайн",
     *                  "admin_suffix": "test",
     *                  "description": "",
     *                  "picture": null,
     *                  "first_status": "0",
     *                  "user_type": "all",
     *                  "target": "all",
     *                  "delivery": [],
     *                  "public": "1",
     *                  "default_payment": "0",
     *                  "commission": "0",
     *                  "commission_include_delivery": "0",
     *                  "commission_as_product_discount": "0",
     *                  "class": "yandexkassaapi",
     *                  "min_price": null,
     *                  "max_price": null,
     *                  "success_status": "12",
     *                  "holding_status": "7",
     *                  "holding_cancel_status": "8",
     *                  "create_cash_receipt": "0",
     *                  "payment_method": "0",
     *                  "create_order_transaction": "0",
     *                  "show_on_partners": [],
     *                  "saved_method_list": [
     *                      {
     *                          "id": "1",
     *                          "external_id": "123132-1321-1231-1231-123123123",
     *                          "type": "card",
     *                          "subtype": "MasterCard",
     *                          "title": "*4444",
     *                          "user_id": "2",
     *                          "payment_type": "yandexkassaapi",
     *                          "payment_type_unique": "762551",
     *                          "save_date": "2022-05-19 20:39:10",
     *                          "data": {
     *                          "first6": "555555",
     *                          "last4": "4444",
     *                          "expiry_year": "2029",
     *                          "expiry_month": "12",
     *                          "card_type": "MasterCard",
     *                          "issuer_country": "US"
     *                          },
     *                          "_data": "a:6:{s:6:\"first6\";s:6:\"555555\";s:5:\"last4\";s:4:\"4444\";s:11:\"expiry_year\";s:4:\"2029\";s:12:\"expiry_month\";s:2:\"12\";s:9:\"card_type\";s:10:\"MasterCard\";s:14:\"issuer_country\";s:2:\"US\";}",
     *                          "is_default": "1",
     *                          "deleted": "0"
     *                      },
     *                      {
     *                          "id": "2",
     *                          "external_id": "321321-3211-3211-3211-321321321",
     *                          "type": "card",
     *                          "subtype": "Visa",
     *                          "title": "*1111",
     *                          "user_id": "2",
     *                          "payment_type": "yandexkassaapi",
     *                          "payment_type_unique": "762551",
     *                          "save_date": "2022-06-29 13:25:28",
     *                          "data": {
     *                          "first6": "411111",
     *                          "last4": "1111",
     *                          "expiry_year": "2026",
     *                          "expiry_month": "12",
     *                          "card_type": "Visa",
     *                          "issuer_country": "US"
     *                          },
     *                          "_data": "a:6:{s:6:\"first6\";s:6:\"411111\";s:5:\"last4\";s:4:\"1111\";s:11:\"expiry_year\";s:4:\"2026\";s:12:\"expiry_month\";s:2:\"12\";s:9:\"card_type\";s:4:\"Visa\";s:14:\"issuer_country\";s:2:\"US\";}",
     *                          "is_default": "0",
     *                          "deleted": "0"
     *                      }
     *                  ]
     *                  }
     *              ],
     *          "add_method_urls": {
     *              "14": "http://127.0.0.1/onlinepay/pay/?params[type]=save_payment_method&params[payment}=1&sign=123123123132"
     *          },
     *          "user": {
     *              "id": "1",
     *              "name": "Иван",
     *              "surname": "Иванов",
     *              "midname": "Иванович",
     *              "e_mail": "user@example.com",
     *              "login": "user",
     *              "phone": "+71234567891",
     *              "sex": "",
     *              ...
     *          }
     *      }
     * }
     * </pre>
     *
     * @return array В случае успеха возвращает список способов оплат с сохраненными картами, url для добавления новой карты, сведения по пользователю.
     *
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token, $saved_method_id, $payment_id)
    {
        $response = [];

        if ($user = $this->token->getUser()) {
            try {
                $saved_method = new SavedPaymentMethod($saved_method_id);
                $payment = new Payment($payment_id);
                $payment_type = $payment->getTypeObject();

                if (empty($saved_method['id']) || $saved_method['user_id'] != $user['id']) {
                    $response['error'] = 'Указанный сохранённый способ платежа не существует';
                }
                if (empty($payment['id'])) {
                    $response['error'] = 'Указанный способ оплаты не существует';
                }
                if (!($payment_type instanceof InterfaceRecurringPayments) || $payment_type->getRecurringPaymentsType() == InterfaceRecurringPayments::RECURRING_TYPE_NONE) {
                    $response['error'] = 'Указанный способ оплаты не поддерживает рекуррентные платежи';
                }

                if ($saved_method['payment_type'] != $payment_type->getShortName() || $saved_method['payment_type_unique'] != $payment_type->getTypeUnique()) {
                    $response['error'] = 'Сохранённый способ платежа не принадлежит данному способу оплаты';
                }
                $saved_method['deleted'] = 1;
                if ($saved_method->update()) {
                    $response = SavedPaymentMethodApi::getSavedPaymentMethods($user);
                }
            } catch (ShopException $e) {
                $response['error'] = $e->getMessage();
            }
        }

        return $response;
    }
}
