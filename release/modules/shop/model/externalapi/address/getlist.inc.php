<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Address;

use ExternalApi\Model\AbstractMethods\AbstractGetList;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils;
use Shop\Model\AddressApi;
use Shop\Model\Orm\Address;
use RS\Orm\Type;

/**
 * Возвращает список адресов, которые можно указать в заказе
 */
class GetList extends AbstractGetList
{
    /**
     * Возвращает объект выборки объектов
     *
     * @return \RS\Module\AbstractModel\EntityList
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new AddressApi();
        }
        return $this->dao;
    }

    /**
     * Устанавливает фильтр для выборки
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param array $filter
     *
     * @throws ApiException
     */
    public function setFilter($dao, $filter)
    {
        $dao->setFilter($this->makeFilter($filter));
        $dao->setFilter('deleted', 0);
    }



    /**
     * Возвращает возможный ключи для фильтров
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
            'user_id' => [
                'func' => self::FILTER_TYPE_EQ,
                'title' => t('ID Пользователя (обязательно, если не задан order_id)'),
                'type' => 'integer',
            ],
            'order_id' => [
                'func' => self::FILTER_TYPE_EQ,
                'title' => t('ID Заказа (обязательно, если не задан user_id)'),
                'type' => 'integer',
            ],
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
        $result = [];
        (new Address())
            ->getPropertyIterator()->append([
                'short_view' => (new Type\Varchar())
                    ->setVisible(true),
                'full_view' => (new Type\Varchar())
                    ->setVisible(true),
            ]);

        foreach($dao->getList($page, $pageSize) as $address) {
            $address['short_view'] = $address->getLineView(false);
            $address['full_view'] = $address->getLineView(true);

            $result[] = Utils::extractOrm($address);
        }

        return $result;
    }

    /**
     * Возвращает список адресов, доступных для использования в заказе
     * ---
     * Обязательно должен быть установлен фильтр по user_id или order_id
     *
     * @param string $token Авторизационный токен
     * @param array $filter Фильтр, поддерживает в ключах поля: #filters-info
     * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
     * @param integer $page Номер страницы, начинается с 1
     * @param mixed $pageSize Размер страницы (0 - все элементы на одной странице)
     *
     * @example GET /api/methods/address.getList?token=311211047ab5474dd67ef88345313a6e479bf616&filter[user_id]=2
     *
     * Ответ:
     * <pre>
     * {
     *       "response": {
     *           "summary": {
     *               "page": 1,
     *               "pageSize": "0",
     *               "total": "1"
     *           },
     *           "list": [
     *               {
     *                   "id": "201",
     *                   "user_id": "2",
     *                   "order_id": "0",
     *                   "zipcode": "350000",
     *                   "country": "Россия",
     *                   "region": "Краснодарский край",
     *                   "city": "Краснодар",
     *                   "address": "ул. Тестовая, 404, кв. 503",
     *                   "street": "",
     *                   "house": "",
     *                   "block": "",
     *                   "apartment": "",
     *                   "entrance": "",
     *                   "entryphone": "",
     *                   "floor": "",
     *                   "subway": "",
     *                   "city_id": "3375",
     *                   "region_id": "3374",
     *                   "country_id": "1",
     *                   "deleted": "0",
     *                   "extra": [],
     *                   "_extra": null,
     *                   "coords": "",
     *                   "short_view": "ул. Тестовая, 404, кв. 503",
     *                   "full_view": "350000, Россия, Краснодарский край, Краснодар, ул. Тестовая, 404, кв. 503"
     *               }
     *           ]
     *       }
     *   }
     * </pre>
     *
     * @return array
     */
    public function process($token, $filter = [], $sort = 'id desc', $page = "1", $pageSize = "0")
    {
        if (empty($filter['user_id']) && empty($filter['order_id'])) {
            throw new ApiException(t('Обязательно должен быть установлен фильтр по user_id или order_id'));
        }

        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}