<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Region;

use ExternalApi\Model\AbstractMethods\AbstractGetList;
use Shop\Model\RegionApi;

class GetCountryList extends AbstractGetList
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
            $this->dao->setFilter('parent_id', 0);
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
                'title' => t('Название страны'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
        ];
    }

    /**
     * Выполняет запрос на выборку стран
     *
     * @param string $token Авторизационный токен
     * @param array $filter Массив из фильтров для применения. Возможные ключи: #filters-info
     * @param string $sort Сортировка. Возможные значения: #sort-info
     * @param string $page Текущий номер страницы
     * @param string $pageSize Размер элементов в порции
     *
     * @return array Возвращает список стран
     * @example GET /api/methods/region.getCountryList?token=311211047ab5474dd67ef88345313a6e479bf616
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "summary": {
                    "page": "1",
                    "pageSize": "0",
                    "total": "2"
                },
                "list": [
                    {
                        "id": "21486",
                        "title": "Украина",
                        "parent_id": "0",
                        "sortn": "100",
                        "fias_guid": "",
                        "kladr_id": "",
                        "type_short": ""
                    },
                    {
                        "id": "1",
                        "title": "Россия",
                        "parent_id": "0",
                        "sortn": "100",
                        "fias_guid": null,
                        "kladr_id": null,
                        "type_short": null
                    }
                ]
            }
        }
     * </pre>
     */
    protected function process($token,
                               $filter = [],
                               $sort = 'id desc',
                               $page = "1",
                               $pageSize = "0")
    {
        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}