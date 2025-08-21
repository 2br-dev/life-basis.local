<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\ExternalApi\Task;

use Crm\Model\TaskFilterApi;
use ExternalApi\Model\AbstractMethods\AbstractGetList;
use YandexMoney\Exceptions\APIException;

/**
 * Класс реализует метод API, который возвращает список сохраненных ранее фильтров для задач
 */
class GetFilterList extends AbstractGetList
{
    /**
     * Возвращает объект выборки объектов
     *
     * @return \RS\Module\AbstractModel\EntityList
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new TaskFilterApi();
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
        $dao->setFilter([
            'user_id' => $this->token['user_id']
        ]);

        parent::setFilter($dao, $filter);
    }

    /**
     * Возвращает возможные значения для сортировки
     *
     * @return array
     */
    public function getAllowableOrderValues()
    {
        return ['sortn', 'title', 'id', 'id desc'];
    }

    /**
     * Возвращает список пресетов фильтров для задач для текущего пользователя.
     * ---
     * Объект содержит необходимые сведения для быстрой установки фильтра по задачам
     *
     * @param string $token Авторизационный токен
     * @param array $filter Массив из фильтров для применения (не используется)
     * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
     * @param string $page Текущий номер страницы. 0 - возвращать все элементы
     * @param string $pageSize Размер элементов в порции, если $page > 0
     *
     * @example GET /api/methods/task.getFilterList?token=311211047ab5474dd67ef88345313a6e479bf616
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "summary": {
                    "page": "0",
                    "pageSize": "20",
                    "total": "5"
                },
                "list": [
                    {
                        "id": "17",
                        "title": "Входящие",
                        "filters_arr": {
                            "implementer_user_id": "2"
                        }
                    },
                    {
                        "id": "18",
                        "title": "Исходящие",
                        "filters_arr": {
                            "creator_user_id": "2"
                        }
                    },
                    {
                        "id": "22",
                        "title": "Мои задачи",
                        "filters_arr": {
                            "implementer_user_id": "2"
                        }
                    },
                    {
                        "id": "23",
                        "title": "Не архивные",
                        "filters_arr": {
                            "is_archived": "0"
                        }
                    },
                    {
                        "id": "24",
                        "title": "Я исполнитель",
                        "filters_arr": {
                            "implementer_user_id": "13"
                        }
                    }
                ]
            }
        }
     * </pre>
     *
     * @return array Возвращает список объектов, описывающих сохраненный фильтр
     */
    protected function process($token,
                               $filter = [],
                               $sort = 'sortn',
                               $page = "0",
                               $pageSize = "20")
    {
        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}