<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Reservation;

use Catalog\Model\PreviewDataHelper;
use ExternalApi\Model\AbstractMethods\AbstractGetList;
use ExternalApi\Model\Utils;
use Shop\Model\ApiUtils;
use Shop\Model\Orm\Reservation;
use Shop\Model\ReservationApi;
use RS\Orm\Type;

/**
* Возвращает список предварительных заказов
*/
class GetList extends AbstractGetList
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
            self::RIGHT_LOAD => t('Загрузка списка объектов')
        ];
    }
    
    /**
    * Возвращает возможные значения для сортировки
    * 
    * @return array
    */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'dateof', 'dateof desc', 'product_title', 'product_title desc'];
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
            'query' => [
                'title' => t('Универсальный поиск по различным полям. В текущее время ищет по ID товара, названию товара, артикулу товара, комплектации, телефону и email пользователя.'),
                'type' => 'string',
                'func' => 'fullSearch'
            ],
            'id' => [
                'title' => t('ID покупки в один клик. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'user_id' => [
                'title' => t('ID привязанного пользователя. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'product_id' => [
                'title' => t('ID товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_IN
            ],
            'product_barcode' => [
                'title' => t('Артикул товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'product_title' => [
                'title' => t('Наименование товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'offer' => [
                'title' => t('Наименование комплектации, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'phone' => [
                'title' => t('Номер телефона, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'email' => [
                'title' => t('E-mail пользователя, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'status' => [
                'title' => t('Статус, массив значений'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_EQ,
                'values' => [\Shop\Model\Orm\Reservation::STATUS_OPEN, \Shop\Model\Orm\Reservation::STATUS_CLOSE]
            ]
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку предварительных заказов
     *
     * @return ReservationApi
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new ReservationApi();
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
        /**
         * @var Reservation[] $list
         */
        $list = $dao->getList($page, $pageSize);

        $reservation = new Reservation();
        $reservation->appendProperty([
            'preview_data' => (new Type\MixedType())
                ->setVisible(true, 'app')
        ]);

        Get::appendReservationProperties($reservation);
        foreach($list as $reservation) {
            Get::appendReservationDynamicValues($reservation);
            $this->addPreviewData($reservation);
        };

        return Utils::extractOrmList($list);
    }

    /**
     * Добавляет к заказу выборку наиболее важных данных для отображения в списках
     *
     * @param Reservation $reservation
     * @return void
     */
    protected function addPreviewData($reservation)
    {
        $preview_data = new PreviewDataHelper();

        $user = $reservation->getUser();
        $user_full_name = $user->getFio();
        if ($user_full_name) {
            $preview_data->addTextRow(t('Покупатель'), $user_full_name);
        }

        if ($reservation['phone']) {
            $preview_data->addTextRow(t('Телефон'), $reservation['phone']);
        }

        if ($reservation['email']) {
            $preview_data->addEmailRow(t('E-mail'), $reservation['email']);
        }

        $reservation['preview_data'] = $preview_data->getPreviewData();
    }

    /**
     * Возвращает готовое условие для установки фильтра. (Тип фильтра - частичное совпадение %like%)
     *
     * @param string $key - поле фильтрации
     * @param mixed $value - значение фильтра
     * @param array $filters - весь список фильтров
     * @param array $filter_settings - параметры фильтра,
     * @return array
     */
    protected function makeFilterFullSearch($key, $value, $filters, $filter_settings)
    {
        $dao = $this->getDaoObject();
        $q = $dao->queryObj();
        $q->select('A.*');
        $q->where("(id = '#term' OR product_id = '#term' OR product_barcode like '%#term%' OR product_title like '%#term%' OR offer like '%#term%' OR phone like '%#term%' OR email like '%#term%')", [
            'term' => $value
        ]);

        return [];
    }


    /**
     * Возвращает список предварительных заказов
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     *
     * @example GET /api/methods/reservation.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/reservation.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[title]=
     *
     * Ответ:
     * <pre>
     * {
     *   "response": {
     *       "summary": {
     *           "page": 1,
     *           "pageSize": 1000,
     *           "total": "1"
     *       },
     *       "list": [
     *           {
     *               "id": "1",
     *               "product_id": "1",
     *               "product_barcode": null,
     *               "product_title": "Моноблок Acer Aspire Z5763",
     *               "offer_id": "104",
     *               "currency": "RUB",
     *               "multioffer": [],
     *               "amount": 2,
     *               "phone": "+79628678430",
     *               "email": "admin@admin.ru",
     *               "is_notify": "1",
     *               "dateof": "2019-12-04 12:47:22",
     *               "user_id": "1",
     *               "status": "open",
     *               "status_color": "#0fd71e",
     *               "comment": null,
     *               "partner_id": "0",
     *               "unit": "шт.",
     *               "single_cost": "14900.00",
     *               "single_cost_formatted": "14 900 р.",
     *               "total_cost": "59600.00",
     *               "total_cost_formatted": "59 600 р."
     *           }
     *       ]
     *   }
     * }
     * </pre>
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token, $filter = [], $sort = "dateof DESC", $page = 1, $pageSize = 1000)
    {
        $result = parent::process($token, $filter, $sort, $page, $pageSize);
        $result['response']['statuses'] = ApiUtils::getReservationStatuses();

        return $result;
    }
}
