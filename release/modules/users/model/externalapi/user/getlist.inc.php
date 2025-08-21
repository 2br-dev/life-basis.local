<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;

use ExternalApi\Model\AbstractMethods\AbstractGetList;
use ExternalApi\Model\Exception as ApiException;
use RS\Config\Loader;
use Users\Model\Api;

/**
 * Метод для получения списка пользователей
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
            $this->dao = new Api();
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
            'query' => [
                'title' => t('Поисковая строка по логину, email, фамилии, имени, телефону, компании, инн'),
                'func' => 'fullSearch',
                'type' => 'string'
            ],
            'is_courier' => [
                'title' => t('Показывать только пользователей, принадлежащих группе пользователей, связанной с курьерами (необходимо наличие модуля Магазин)'),
                'func' => 'isCourier',
                'type' => 'integer'
            ],
            'is_manager' => [
                'title' => t('Показывать только пользователей, принадлежащих группе пользователей, связанной с менеджерами (необходимо наличие модуля Магазин)'),
                'func' => 'isManager',
                'type' => 'integer'
            ]
        ];
    }

    /**
     * Устанавливает фильтр по группе, связанной с курьерами
     *
     * @param $key
     * @param $value
     * @param $filters
     * @param $filter_settings
     * @return array
     */
    protected function makeFilterIsCourier($key, $value, $filters, $filter_settings)
    {
        if ($shop_config = Loader::byModule('shop')) {
            if ($shop_config['courier_user_group']) {
                return [
                    'group' => $shop_config['courier_user_group']
                ];
            } else {
                throw new ApiException(t('Группа пользователей, связанная с курьерами не установлена в настройках модуля Магазин'), ApiException::ERROR_INSIDE);
            }
        } else {
            throw new ApiException(t('Для использования фильтра is_courier необходимо наличие модуля Магазин'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
    }

    /**
     * Устанавливает фильтр по группе, связанной с менеджерами
     *
     * @param $key
     * @param $value
     * @param $filters
     * @param $filter_settings
     * @return array
     */
    protected function makeFilterIsManager($key, $value, $filters, $filter_settings)
    {
        if ($shop_config = Loader::byModule('shop')) {
            if ($shop_config['manager_group']) {
                return [
                    ' group' => $shop_config['manager_group']
                ];
            } else {
                throw new ApiException(t('Группа пользователей, связанная с менеджерами не установлена в настройках модуля Магазин'), ApiException::ERROR_INSIDE);
            }
        } else {
            throw new ApiException(t('Для использования фильтра is_manager необходимо наличие модуля Магазин'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
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
        $words = explode(" ", $value);

        $search_fields = ['login', 'e_mail', 'surname', 'name', 'company', 'company_inn', 'phone'];
        $q = $this->getDaoObject()->queryObj();

        $q->openWGroup();
        if (count($words) == 1) {
            foreach ($search_fields as $field) {
                $q->where("$field like '%#term%'", ['term' => $value], 'OR');
            }
        }else{ //Если несколько полей, проверяем по ФИО
            foreach ($words as $word) {
                if (!empty($word)){
                    $q->where("CONCAT_WS('', `surname`, `name`, `midname`) like '%#term%'", ['term' => $word], 'AND');
                }
            }
        }
        $q->closeWGroup();

        return [];
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
        foreach($dao->getList($page, $pageSize) as $user) {
            $result[] = [
                'id' => $user['id'],
                'fullname' => $user->getFio(),
                'name' => $user['name'],
                'surname' => $user['surname'],
                'midname' => $user['midname'],
                'is_company' => (bool)$user['is_company'],
                'company' => $user['company'],
                'company_inn' => $user['company_inn'],
            ];
        }

        return $result;
    }

    /**
     * Возвращает список пользователей
     *
     * @param string $token Авторизационный токен
     * @param array $filter Массив из фильтров для применения.
     * Возможные ключи:
     * #filters-info
     * @param string $sort Сортировка
     * #sort-info
     * @param integer $page Текущий номер страницы
     * @param integer $pageSize Количество элементов на странице
     * @return array
     * @example GET /api/methods/user.getList?token=5012d66bbf868da9c0c54889cd246db0e14e1232&filter[query]=Иванов
     *
     * Ответ:
     * <pre>
     * {
     *       "response": {
     *           "summary": {
     *               "page": "1",
     *               "pageSize": 1,
     *               "total": "94"
     *           },
     *           "list": [
     *               {
     *                   "id": "107",
     *                   "fullname": "Иванов Петр Семенович",
     *                   "name": "Петр",
     *                   "surname": "Иванов",
     *                   "midname": "Семенович",
     *                   "is_company": true,
     *                   "company": "ООО Ромашка",
     *                   "company_inn": "1234567890"
     *               }
     *           ]
     *       }
     *   }
     * </pre>
     */
    protected function process($token,
                               $filter = [],
                               $sort = 'id desc',
                               $page = "1",
                               $pageSize = "10")
    {
        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}