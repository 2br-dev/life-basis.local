<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\BonusCard;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Shop\Model\BonusCardsApi;

/**
* Возвращает поля, необходимые для регистрации бонусной карты
*/
class GetFields extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

    public
        /**
         * @var BonusCardsApi
         */
        $bonus_card_api;

    function __construct()
    {
        parent::__construct();
        $this->bonus_card_api = new BonusCardsApi();
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
            self::RIGHT_LOAD => t('Загрузка списка полей, необходимых для регистрации бонусной карты.')
        ];
    }

    /**
     * Возвращает поля, необходимые для регистрации бонусной карты
     *
     * @param string $token Авторизационный токен
     *
     * @example GET /api/methods/bonusCard.getFields?token=c4b7a1036d7dbcbf979a40058088297486058519
     * Ответ:
     * <pre>
     * {
     *      "response": [
     *         {
     *             "alias": "phone",
     *             "title": "Телефон",
     *             "type": "text",
     *             "options": null,
     *             "value": "+71234567891",
     *             "user_field": "phone"
     *         },
     *         {
     *             "alias": "birthday",
     *             "title": "Дата рождения",
     *             "type": "date",
     *             "options": null,
     *             "value": null,
     *             "user_field": "birthday"
     *         }
     *     ]
     * }
     * </pre>
     *
     * Возвращает объект.
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token)
    {
        $result = [];

        $fields = $this->bonus_card_api->getAdditionalFields();

        if ($user = $this->token->getUser()) {
            foreach ($fields as $key => $field) {
                if (isset($user[$field['user_field']])) {
                    $fields[$key]['value'] = $user[$field['user_field']];
                }
            }
        }

        $result['response'] = $fields;

        return $result;
    }
}
