<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\OneClick;

use Catalog\Model\ApiUtils;
use Catalog\Model\OneClickItemApi;
use Catalog\Model\Orm\OneClickItem;
use Catalog\Model\PreviewDataHelper;
use ExternalApi\Model\AbstractMethods\AbstractGetList;
use ExternalApi\Model\Utils;
use RS\Orm\Type;

/**
* Возвращает список покупок в один клик
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
        return ['id', 'id desc', 'dateof', 'dateof desc', 'title', 'title desc'];
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
            'query' => [
                'title' => t('Универсальный поиск по различным полям'),
                'type' => 'string',
                'func' => 'fullSearch'
            ],
            'id' => [
                'title' => t('ID покупки в один клик. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'title' => [
                'title' => t('Номер сообщения, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'user_id' => [
                'title' => t('ID привязанного пользователя, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_IN
            ],
            'user_phone' => [
                'title' => t('Номер телефона, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'user_fio' => [
                'title' => t('ФИО пользователя, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'status' => [
                'title' => t('Статус'),
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',
                'values' => [
                    OneClickItem::STATUS_NEW,
                    OneClickItem::STATUS_VIEWED,
                    OneClickItem::STATUS_CANCELLED,
                ]
            ],
        ];
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
        $like_mask = $filter_settings['like_mask'] ?? '%like%';

        return [
            [
                "number:$like_mask" => $value,
                "|user_fio:$like_mask" => $value,
                "|user_phone:$like_mask" => $value,
                "|title:$like_mask" => $value,
                "|stext:$like_mask" => $value,
            ]
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку покупок в один клик
     *
     * @return OneClickItemApi
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new OneClickItemApi();
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
         * @var $list OneClickItem[]
         */
        $list = $dao->getList($page, $pageSize);

        $one_click = new OneClickItem();
        $one_click->appendProperty([
            'preview_data' => (new Type\MixedType())
                ->setVisible(true, 'app')
        ]);
        Get::appendDynamicProperties($one_click);

        foreach($list as $one_click) {
            Get::appendDynamicValues($one_click);
            $this->addPreviewData($one_click);
        }

        return Utils::extractOrmList($list);
    }

    /**
     * Добавляет к заказу выборку наиболее важных данных для отображения в списках
     *
     * @param OneClickItem $one_click
     * @return void
     */
    protected function addPreviewData($one_click)
    {
        $preview_data = new PreviewDataHelper();
        $user = $one_click->getUser();
        $user_full_name = $one_click['user_fio'] ?: $user->getFio();
        $preview_data->addTextRow(t('Покупатель'), $user_full_name ?: t('Не указан'));

        if ($user['phone']) {
            $preview_data->addPhoneRow(t('Телефон'), $user['phone']);
        }

        $one_click['preview_data'] = $preview_data->getPreviewData();
    }

    /**
     * Возвращает список покупок в один клик
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     * @param string $fulltext_filter Полнотекстовый фильтр. (В текущее время ищет по номеру покупки в 1 клик, названии, номеру телефона пользователя, ФИО пользователя, товарам в покупке в 1 клик)
     *
     * @example GET /api/methods/oneclick.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/oneclick.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[title]=
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
     *          {
     *               "id": "1",
     *               "user_fio": "Супервизор",
     *               "user_phone": "+79628678430",
     *               "title": "Покупка №1 Супервизор (+79628678430)",
     *               "dateof": "2019-12-04 11:55:18",
     *               "status": "new",
     *               "ip": "127.0.0.1",
     *               "currency": "RUB",
     *               "sext_fields": [],
     *               "stext": [
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
     *               "partner_id": "0"
     *          }
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
        $result['response']['statuses'] = ApiUtils::getOneClickStatuses();

        return $result;
    }
}