<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\User;
use \ExternalApi\Model\Exception as ApiException;
use Shop\Model\AddressApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;

/**
* Возвращает список адресов привязанных к пользователю по токену
*/
class GetAddresses extends \ExternalApi\Model\AbstractMethods\AbstractGetList
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
                'title' => t('ID пользователя'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
            'deleted' => [
                'title' => t('Удалён товар или нет'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
        ];
    }  

    
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc'];
    }
 
 
    /**
    * Возвращает список адресов привязанных к пользователю
    * 
    * @param string $token Авторизационный token
    * @param array $_filter Зарезервировано для фильтров #filters-info
    * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
    * @param integer $page Номер страницы, начинается с 1
    * @param mixed $pageSize Размер страницы
    * 
    * @example GET /api/methods/user.getaddresess?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
    * Ответ:
    * <pre>
    *{ 
    *    "response": { 
    *        "summary": { 
    *            "page": "1", 
    *            "pageSize": "20", 
    *            "total": "3" 
    *        }, 
    *        "list": [ 
    *            { 
    *                "id": "2", 
    *            }
    *        ] 
    *    } 
    *} 
    *</pre>
    * 
    * @return array Возвращает список адресов пользователя
    */
    protected function process($token,
                               $filter = [],
                               $sort = 'id desc',
                               $page = "1", 
                               $pageSize = "50")
    {
        $user = $this->token->getUser();
        return parent::process($token, ['user_id' => $user['id'], 'deleted' => 0], $sort, $page, $pageSize);
    }

    /**
     * Возвращает список адресов привязанных к пользователю
     * ---
     * Пользователь определяется при передаче токена.
     * Возвращается список адресов, доступных по текущему заказу
     *
     * @param string $token Авторизационный token
     *
     * @example GET /api/methods/user.getaddresess?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
     * Ответ:
     * <pre>
     *{
     *    "response": {
     *        "summary": {
     *            "page": "1",
     *            "pageSize": "20",
     *            "total": "3"
     *        },
     *        "list": [
     *            {
     *                "id": "2",
     *            }
     *        ]
     *    }
     *}
     *</pre>
     *
     * @return array Возвращает список адресов пользователя
     */
    protected function processVer2($token)
    {
        $order = Order::currentOrder();
        $user = $this->token->getUser();
        $this->dao = $this->getDaoObject();

        return $response = [
            'response' => [
                $this->getObjectSectionName() => \ExternalApi\Model\Utils::extractOrmList($this->dao->getCheckoutUserAddresses($order, $user, true)),
            ]
        ];
    }
}
