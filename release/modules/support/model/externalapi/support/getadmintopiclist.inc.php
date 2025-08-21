<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\ExternalApi\Support;

use ExternalApi\Model\AbstractMethods\AbstractGetList;
use ExternalApi\Model\Utils;
use Support\Model\TopicApi;

/**
* Возвращает список обращений в поддержку - администратору
*/
class GetAdminTopicList extends AbstractGetList
{
    /**
     * Возвращает объект выборки объектов
     *
     * @return TopicApi
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new TopicApi();
        }
        return $this->dao;
    }

    /**
     * Устанавливает фильтр для выборки
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param array $filter
     * @return void
     */
    public function setFilter($dao, $filter)
    {
        parent::setFilter($dao, $filter);
    }

    /**
     * Возвращает возможные значения для сортировки
     *
     * @return array
     */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'updated', 'updated desc'];
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
            'title' => [
                'title' => t('Тема, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'number' => [
                'title' => t('Уникальный номер темы'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
            'status' => [
                'title' => t('status'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE,
                'values' => [
                    'answered',
                    'open',
                    'closed',
                ]
            ],
        ];
    }

    /**
     * Возвращает список объектов
     *
     * @param TopicApi $dao - API списка обращений
     * @param integer $page - номер страницы
     * @param integer $pageSize - количество элементов
     * @return array
     */
    public function getResultList($dao, $page, $pageSize)
    {
        $list = $dao->getList($page, $pageSize);

        if (!empty($list)) {
            GetTopicList::appendDynamicProperties($dao->getElement());
            $dao->getElement()->__newadmcount->setVisible(true, 'app');

            foreach ($list as $key => $topic) {
                GetTopicList::appendDynamicValues($topic);
            }
        }

        return Utils::extractOrmList($list);
    }

    /**
     * Возвращает список обращений в поддержку - администратору
     *
     * @param string $token Авторизационный token
     * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
     * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
     * @param integer $page Номер страницы, начинается с 1
     * @param mixed $pageSize Размер страницы
     *
     * @example GET /api/methods/support.getTopicList?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "summary": {
     *              "page": "1",
     *              "pageSize": "20",
     *              "total": "2"
     *          },
     *          "list": [
     *              {
     *                  "id": "1",
     *                  "title": "Обращение номер один",
     *                  "number": "111111",
     *                  "user_id": "1",
     *                  "msgcount": "6",
     *                  "newcount": "1",
     *                  "status": "answered"
     *              },
     *              {
     *                  "id": "2",
     *                  "title": "Обращение номер два",
     *                  "number": "222222",
     *                  "user_id": "1",
     *                  "msgcount": "1",
     *                  "newcount": "0",
     *                  "status": "open"
     *              },
     *              ...
     *          ]
     *      }
     * }
     * </pre>
     *
     * @return array
     */
    protected function process($token, $filter = [], $sort = 'updated DESC', $page = '1', $pageSize = '20')
    {
        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}