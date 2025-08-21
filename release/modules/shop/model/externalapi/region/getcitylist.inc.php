<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Region;

use ExternalApi\Model\AbstractMethods\AbstractFilteredList;
use ExternalApi\Model\Utils;
use Shop\Model\RegionApi;

class GetCityList extends AbstractFilteredList
{
    /**
     * Возвращает объект выборки объектов
     *
     * @return \RS\Module\AbstractModel\EntityList
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new RegionApi();
            $this->dao->setFilter([
                'parent_id:>' => 0,
                'is_city' => 1
            ]);
        }

        return $this->dao;
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
        return Utils::extractOrmList( $dao->getList($page, $pageSize) );
    }

    /**
     * Выполняет запрос на выборку городов
     *
     * @param string $token Авторизационный токен
     * @param string $region_id ID региона
     * @param string $city_term Название города (Не менее 2х букв)
     * @param string $sort Сортировка. Возможные значения: #sort-info
     * @param string $page Текущий номер страницы
     * @param string $pageSize Размер элементов в порции
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     *
     * @example GET /api/methods/region.getRegionList?token=311211047ab5474dd67ef88345313a6e479bf616&region_id=135&city_term=Красно
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "summary": {
                    "page": "1",
                    "pageSize": "1",
                    "total": "1"
                },
                "list": [
                    {
                        "id": "21482",
                        "title": "Краснодар",
                        "parent_id": "135",
                        "sortn": "100",
                        "fias_guid": null,
                        "kladr_id": "9900000000000",
                        "type_short": "г"
                    }
                ]
            }
        }
     * </pre>
     */
    protected function process($token,
                               $region_id,
                               $city_term,
                               $sort = 'id desc',
                               $page = "1",
                               $pageSize = "20")
    {
        if (mb_strlen($city_term) >= 2) {
            $this->dao = $this->getDaoObject();
            $this->dao->setFilter([
                'parent_id' => $region_id,
                'title:%like%' => $city_term
            ]);
            $this->setOrder($this->dao, $sort);
            $total = $this->getResultCount($this->dao);
            $list = $this->getResultList($this->dao, $page, $pageSize);
        } else {
            $total = 0;
            $list = [];
        }

        $response = [
            'response' => [
                'summary' => [
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'total' => $total
                ],
                $this->getObjectSectionName() => $list
            ]
        ];

        return $response;
    }
}