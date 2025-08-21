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

class GetRegionList extends AbstractFilteredList
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
                'is_city' => 0
            ]);
        }

        return $this->dao;
    }

    /**
     * Возвращает возможные ключи для фильтров
     *
     * @return [
     *   'поле' => [
     *       'title' => 'Описание поля. Если не указано, будет загружено описание из ORM Объекта'
     *       'type' => 'тип значения',
     *       'func' => 'постфикс для функции makeFilter в текущем классе, которая будет готовить фильтр, например eq',
     *       'values' => [возможное значение1, возможное значение2]
     *   ]
     * ]
     */
    public function getAllowableFilterKeys()
    {
        return [
            'title' => [
                'title' => t('Название региона'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'country_id' => [

            ]
        ];
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
     * Выполняет запрос на выборку областей страны
     *
     * @param string $token - авторизационный токен
     * @param string $country_id - ID страны
     * @param array $filter - массив из фильтров для применения
     * @param string $sort Сортировка. Возможные значения: #sort-info
     * @param string $page - текущий номер страницы
     * @param string $pageSize - размер элементов в порции
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     *
     * @example GET /api/methods/region.getRegionList?token=311211047ab5474dd67ef88345313a6e479bf616&country_id=1
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "summary": {
                    "page": "1",
                    "pageSize": "1",
                    "total": "86"
                },
                "list": [
                    {
                        "id": "21482",
                        "title": "Байконур",
                        "parent_id": "1",
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
                               $country_id,
                               $filter = [],
                               $sort = 'id desc',
                               $page = "1",
                               $pageSize = "0")
    {
        $this->dao = $this->getDaoObject();
        $this->setFilter($this->dao, $filter);
        $this->setOrder($this->dao, $sort);
        $this->dao->setFilter('parent_id', $country_id);

        $response = [
            'response' => [
                'summary' => [
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'total' => $this->getResultCount($this->dao),
                ],
                $this->getObjectSectionName() => $this->getResultList($this->dao, $page, $pageSize),
            ]
        ];

        return $response;
    }
}