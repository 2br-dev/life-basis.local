<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\BonusCard;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use RS\Exception;
use Shop\Model\BonusCardsApi;

/**
* Добавляет бонусную карту
*/
class Add extends AbstractAuthorizedMethod
{
    const RIGHT_ADD = 1;
    protected $bonus_card_api;

    public function __construct()
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
            self::RIGHT_ADD => t('Добавление бонусной карты.')
        ];
    }

    /**
     * Создает бонусную карту для пользователя
     *
     * @param string $token Авторизационный токен
     * @param string $number Номер бонусной карты
     * @param array $data Массив дополнительных сведений
     *
     * @example GET /api/methods/bonusCard.add?token=c4b7a1036d7dbcbf979a40058088297486058519&data[phone]=8800000000&data[sex]=M&data[birthday]=0000-00-00
     * Ответ:
     * <pre>
     * {
     *      "success": true,
     *      "response": {
     *           "id": 35,
     *           "number": "1111100000735",
     *           "user_id": "2",
     *           "save_date": "2022-07-05 17:37:41",
     *           "data": {
     *               "phone": "8800000000",
     *               "sex": "M",
     *               "birthday": "0000-00-00"
     *           },
     *           "barcode_url": "http://127.0.0.1/qrcode/?data=1111100000735&option%5Bw%5D=700&option%5Bh%5D=350&option%5Bs%5D=ean-13-nopad&sign=ceab07355acac0e10a26634768b1c54cf463a170"
     * }
     * </pre>
     *
     * Возвращает объект.
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token, $number = null, $data = [])
    {
        $result = [
            'success' => false,
        ];

        if ($user = $this->token->getUser()) {
            try {
                $card = $this->bonus_card_api->addBonusCard($user->id, $number, $data);
                if ($card) {
                    $result['success'] = true;

                    $card->getPropertyIterator()->append([
                        'barcode_url' => new \RS\Orm\Type\Varchar([
                            'appVisible' => true
                        ]),
                        'data' => new \RS\Orm\Type\Varchar([
                            'appVisible' => true
                        ]),
                    ]);

                    $card['barcode_url'] = $card->getBonusCardBarcode();

                    $result['response'] = \ExternalApi\Model\Utils::extractOrm($card);
                }
            }catch (Exception $e) {
                $result['errors'] = $e->getMessage();
            }
        }

        return $result;
    }
}
