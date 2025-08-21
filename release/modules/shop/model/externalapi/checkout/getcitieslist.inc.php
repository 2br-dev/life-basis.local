<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;

use Shop\Model\Orm\Region;
use Shop\Model\RegionApi;

/**
* Возвращает список городов при поиске города
*/
class GetCitiesList extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false; //Токен не обязателен
    
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
            self::RIGHT_LOAD => t('Загрузка списков')
        ];
    }


    /**
     * Возвращает списки регионов по строке поиска
     *
     * @param $term строка поиска
     * @param $country_id ID страны
     * @param int $is_city искать только города
     * @param int $limit лимит
     * @param string $token Авторизационный токен
     *
     *
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     * @example GET /api/methods/checkout.getCitiesList
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "lists": {
     *           "country" : [
     *              {
     *                "id": 1,
     *                "title": "Россия"
     *              }
     *           ],
     *           "regions" : [
     *              {
     *                "id": "73",
     *                "title": "Адыгея",
     *                "parent_id": "1"
     *              },
     *              ...
     *           ],
     *           "city" : [
     *              {
     *                 "id": "857",
     *                 "title": "Абаза",
     *                 "parent_id": "47",
     *                 "zipcode": "350000",
     *              },
     *              ...
     *           ],
     *         }
     *     }
     * }
     * </pre>
     */
    protected function process($term, $country_id, $is_city = 1, $limit = 20, $token = null)
    {
        $region_api = new RegionApi();

        $region_api->setFilter([
            'title:%like%' => $term,
            'is_city' => $is_city,
        ]);
        $region_api->setOrder('INSTR(title, "#0"), LENGTH(title)', [$term]);

        $region_api = RegionApi::addAffiliateFilter($region_api);

        /** @var Region[] $region_list */
        $region_list = $region_api->getList(1, $limit);
        $result = [];
        foreach ($region_list as $city) {
            $region = $city->getParent();
            $country = $region->getParent();
            if ($country_id && $country->id == $country_id) {
                $result[] = [
                    'label' => $city['title'] . ', ' . $region['title'],
                    'city' => $city['title'],
                    'city_id' => $city['id'],
                    'region' => $region['title'],
                    'region_id' => $region['id'],
                    'country' => $country['title'],
                    'country_id' => $country['id'],
                ];
            }
        }

        
        return $result;
    }
}