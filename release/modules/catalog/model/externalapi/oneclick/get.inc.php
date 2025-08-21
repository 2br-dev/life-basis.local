<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\OneClick;

use Catalog\Model\ApiUtils;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\OneClickItem;
use Catalog\Model\Orm\Product;
use ExternalApi\Model\AbstractMethods\AbstractGet;
use RS\Helper\CustomView;
use RS\Orm\Type;

/**
* Возвращает покупку в один клик по ID
*/
class Get extends AbstractGet
{
    const RIGHT_LOAD = 1;
    
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
            self::RIGHT_LOAD => t('Загрузка объекта')
        ];
    }
    
    /**
    * Возвращает название секции ответа, в которой должен вернуться список объектов
    * 
    * @return string
    */
    public function getObjectSectionName()
    {
        return 'oneclick';
    }

    /**
    * Возвращает объект с которым работает
    *
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\OneClickItem();
    }

    /**
     * Подготавливает объект ($this->object) после его загрузки и перед отдачей
     *
     * @return void
     */
    protected function prepareObject()
    {
        self::appendDynamicProperties($this->object);
        self::appendDynamicValues($this->object);
    }

    /**
     * Добавляет необходимые динамичесние свойства
     *
     * @param OneClickItem $oneclick
     * @return void
     */
    public static function appendDynamicProperties(OneClickItem $oneclick)
    {
        $oneclick
            ->getPropertyIterator()->append([
                'status_title' => (new Type\MixedType())
                    ->setVisible(true),
                'status_color' => (new Type\MixedType())
                    ->setVisible(true),
                'total' => (new Type\MixedType())
                    ->setVisible(true),
                'total_formatted' => (new Type\MixedType())
                    ->setVisible(true),
                'can_create_order' => (new Type\MixedType())
                    ->setVisible(true),
            ]);
    }

    /**
     * Добавляет необходимые динамические значения полей к объекту
     *
     * @param OneClickItem $oneclick
     * @return void
     */
    public static function appendDynamicValues(OneClickItem $oneclick)
    {
        if (!empty($oneclick['stext'])) {
            $total = 0;
            $one_click_data = @unserialize((string)$oneclick['stext']) ?: [];
            $currency = CurrencyApi::getByUid($oneclick['currency']);

            foreach ($one_click_data as $key => $item) {
                if ($item['id'] && is_array($item['offer_fields'])) {
                    $product = new Product($item['id']);
                    $offer = new Offer($item['offer_fields']['offer_id'] ?? 0);
                    $unit = $offer['id'] ? $offer->getUnit() : $product->getUnit();

                    $single_cost = $product->getCost(null, $item['offer_fields']['offer_id'] ?? 0, false);
                    $total_cost = round($single_cost * ($item['offer_fields']['amount'] ?? 1), 2);
                    $one_click_data[$key]['offer_fields']['unit'] = $unit->stitle;
                    $one_click_data[$key]['single_cost'] = $single_cost;
                    $one_click_data[$key]['total_cost'] = $total_cost;
                    $one_click_data[$key]['single_cost_formatted'] = CustomView::cost($single_cost, $currency['stitle'] ?? '');
                    $one_click_data[$key]['total_cost_formatted'] = CustomView::cost($total_cost, $currency['stitle'] ?? '');
                    $total += $total_cost;

                    $one_click_data[$key]['image'] = ApiUtils::prepareImagesSection($product->getMainImage());
                }
            }
            $oneclick['total'] = $total;
            $oneclick['total_formatted'] = CustomView::cost($total, $currency['stitle'] ?? '');
            $oneclick['stext'] = $one_click_data;
        }

        if (!empty($oneclick['sext_fields'])) {
            $oneclick['sext_fields'] = @unserialize((string)$oneclick['sext_fields']) ?: [];
        }

        $oneclick['status_color'] = $oneclick->getStatusColor();
        $oneclick['status_title'] = $oneclick->getStatusTitle();
        $oneclick['can_create_order'] = $oneclick->canCreateOrder();
    }

    /**
     * Возвращает покупку в один клик по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $one_click_id ID покупки в один клик
     *
     *
     * @example GET api/methods/oneclick.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&id=1
     * Ответ
     * <pre>
     * {
     *     "response": {
     *         "oneclick": {
     *             "id": "1",
     *             "user_fio": "Супервизор",
     *             "user_phone": "+79628678430",
     *             "title": "Покупка №1 Супервизор (+79628678430)",
     *             "dateof": "2019-12-04 11:55:18",
     *             "status": "new",
     *             "ip": "127.0.0.1",
     *             "currency": "RUB",
     *             "sext_fields": [],
     *             "stext": [
     *                  {
     *                      "id": "1",
     *                      "title": "Моноблок Acer Aspire Z5763",
     *                      "barcode": "PW.SFNE2.033",
     *                      "offer_fields": {
     *                          "offer": "",
     *                          "offer_id": null,
     *                          "multioffer": [],
     *                          "multioffer_val": [],
     *                          "amount": 1
     *                      }
     *                  }
     *              ],
     *          "partner_id": "0"
     *       }
     *     }
     * }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token, $one_click_id)
    {
        $result = parent::process($token, $one_click_id);
        $result['response']['statuses'] = ApiUtils::getOneClickStatuses();

        return $result;
    }
}
