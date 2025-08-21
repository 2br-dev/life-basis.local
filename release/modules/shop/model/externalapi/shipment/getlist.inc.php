<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Shipment;
use ExternalApi\Model\AbstractMethods\AbstractGetList;
use \ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils;
use Shop\Model\ShipmentApi;

/**
 * Возвращает список отгрузок по заказу
 */
class GetList extends AbstractGetList
{
    /**
     * Возвращает возможные ключи для фильтров
     *
     * @return [
     *   'поле' => [
     *       'type' => 'тип значения'
     *   ]
     * ]
     */
    public function getAllowableFilterKeys()
    {
        return [
            'order_id' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'integer',
            ]
        ];
    }

    /**
     * Возвращает объект выборки объектов
     *
     * @return \RS\Module\AbstractModel\EntityList
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new ShipmentApi();
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
     * Возвращает список отгрузок для заказа
     *
     * @param string $token Авторизационный token
     * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
     * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
     * @param integer $page Номер страницы, начинается с 1
     * @param mixed $pageSize Размер страницы
     *
     * @example GET /api/methods/shipment.getlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "summary": {
     *             "page": "1",
     *             "pageSize": "20",
     *             "total": "1"
     *         },
     *         "list": [
     *             {
     *                 "id": "24",
     *                 "order_id": "1519",
     *                 "date": "2024-06-20 19:32:15",
     *                 "info_order_num": "538534",
     *                 "info_total_sum": "64800.00"
     *             }
     *         ]
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает список объектов
     * @throws ApiException
     */
    protected function process($token,
                               $filter = [],
                               $sort = 'id',
                               $page = "1",
                               $pageSize = "20")
    {

        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}
