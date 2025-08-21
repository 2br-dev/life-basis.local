<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\OneClick;

use Catalog\Model\Orm\OneClickItem;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use RS\Exception;
use ExternalApi\Model\Exception as ApiException;

/**
 * Обновляет статус покупки в 1 клик
 */
class SetStatus extends AbstractAuthorizedMethod
{
    const RIGHT_UPDATE = 1;

    private $statuses;

    function __construct()
    {
        parent::__construct();
        $this->statuses = array_keys(OneClickItem::getStatusTitles());
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
            self::RIGHT_UPDATE => t('Изменение статуса'),
        ];
    }

    /**
     * Обновляет статус покупки в 1 клик
     *
     * @param string $token Авторизационный токен
     * @param integer $one_click_id ID покупки в 1 клик
     * @param string $status статус (возможные значения в ключах OneClickItem::getStatusTitles())
     *
     * @return array Возвращает покупку в 1 клик
     * @example POST /api/methods/oneclick.setStatus?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&one_click_id=1&status=viewed
     *
     * Ответ:
     *  <pre>
     *  {
     *      "response": {
     *          "success": true,
     *          "oneclick": {
     *              "id": "1",
     *              "user_fio": "Супервизор",
     *              "user_phone": "+79628678430",
     *              "title": "Покупка №1 Супервизор (+79628678430)",
     *              "dateof": "2019-12-04 11:55:18",
     *              "status": "new",
     *              "ip": "127.0.0.1",
     *              "currency": "RUB",
     *              "status": "viewed",
     *              "sext_fields": [],
     *              "stext": [
     *                   {
     *                       "id": "1",
     *                       "title": "Моноблок Acer Aspire Z5763",
     *                       "barcode": "PW.SFNE2.033",
     *                       "offer_fields": {
     *                           "offer": "",
     *                           "offer_id": null,
     *                           "multioffer": [],
     *                           "multioffer_val": [],
     *                           "amount": 1
     *                       }
     *                   }
     *               ],
     *           "partner_id": "0"
     *        }
     *      }
     *  }
     *  </pre>
     *
     */
    public function process($token, $one_click_id, $status)
    {
        if (!in_array($status, $this->statuses)) {
            throw new ApiException(t('Неверное значение параметра status'), ApiException::ERROR_WRONG_PARAMS);
        }
        $one_click = new OneClickItem();
        if ($one_click->load($one_click_id)) {
            Get::appendDynamicProperties($one_click);
            try {
                $one_click['status'] = $status;
                $one_click->update();
                Get::appendDynamicValues($one_click);
                return [
                    'response' => [
                        'success' => true,
                        'oneclick' => Utils::extractOrm($one_click)
                    ]
                ];
            } catch(Exception $e) {
                throw new ApiException($e->getMessage(), ApiException::ERROR_WRONG_PARAM_VALUE);
            }

        }else {
            throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }
    }
}