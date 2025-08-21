<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Payment;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Shop\Model\PaymentApi;
use Shop\Model\SavedPaymentMethodApi;
use Users\Model\Orm\User;
use ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список привязанных способов оплат
*/
class GetPaymentMethodsList extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD_SELF = 1;
    const RIGHT_LOAD = 2;

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
            self::RIGHT_LOAD_SELF => t('Загрузка списка своих привязанных способов оплат.'),
            self::RIGHT_LOAD => t('Загрузка списка привязанных способов оплат любого пользователя.')
        ];
    }

    /**
     * Возвращает список способов оплат с сохраненными картами, url для добавления новой карты, сведения по пользователю
     *
     * @param string $token Авторизационный токен
     * @param integer $user_id ID пользователя. Если не передан, то возвращаются сведения по владельцу токена
     *
     * @example GET /api/methods/payment.getPaymentMethodsList?token=123123123123123123123
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
     * Возвращает список объектов и связанные с ним сведения.
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token, $user_id = null)
    {
        $user = $this->token->getUser();

        if ($this->hasRights(self::RIGHT_LOAD)) {
            if ($user_id) {
                $user = new User($user_id);
                if (!$user['id']) {
                    throw new ApiException(t('Пользователь не найден'), ApiException::ERROR_WRONG_PARAMS);
                }
            }
        } elseif ($this->hasRights(self::RIGHT_LOAD_SELF)) {
            if ($user_id && $user_id != $this->token['user_id']) {
                throw new ApiException(t('Недостаточно прав для доступа к данному пользователю'), ApiException::ERROR_METHOD_ACCESS_DENIED);
            }
        }

        return SavedPaymentMethodApi::getSavedPaymentMethods($user);
    }
}
