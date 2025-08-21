<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\ExternalApi\Comment;

use Catalog\Model\Orm\Product;
use Comments\Model\Api;
use ExternalApi\Model\Utils;
use RS\Config\Loader;

/**
* Возвращает список комментариев
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
{
    const RIGHT_LOAD = 1;
    
    protected $token_require = false;
    
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
        return ['id', 'id desc', 'dateof', 'dateof desc', 'user_id', 'user_id desc', 'rate', 'rate desc'];
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
            'id' => [
                'title' => t('ID комментария. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'type' => [
                'title' => t('Тип комментария'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE,
                'values' => self::getFilterByType(),
            ],
            'aid' => [
                'title' => t('ID связанного объекта'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_EQ
            ],
        ];
    }

    /**
     * Возвращает список типов комментария
     *
     * @return array
     * @throws \RS\Exception
     */
    function getFilterByType()
    {
        $api_types = Api::getTypeList();
        $types = [];

        if ($api_types) {
            foreach ($api_types as $type => $annotation) {
                $types[] = quotemeta($type);
            }
        }
        return $types;
    }

    /**
     * Возвращает объект, который позволит производить выборку товаров
     *
     * @return Api
     */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new Api();
            if (Loader::byModule($this)['need_moderate'] === 'Y') {
                $this->dao->setFilter(['moderated' => 1]);
            }
        }
        return $this->dao;
    }

    /**
     * Добавляет секцию с изображениями к товару
     *
     * @param $product - объект товара
     * @return void
     */
    protected function addImageData($product)
    {
        $product->getPropertyIterator()->append([
            'image' => new \RS\Orm\Type\ArrayList([
                'description' => t('Изображения'),
                'appVisible' => true
            ])
        ]);
        $images = [];
        foreach($product->getImages() as $image) {
            $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
        }
        $product['image'] = $images;
    }

    /**
     * Добавляет объект товара к ответу
     *
     * @param $response - Ответ
     * @param $filter_type - Тип комментария
     * @return array
     */
    protected function addProductsData(&$response, $filter_type)
    {
        if (isset($response['response']['list']) && $filter_type == quotemeta('\Catalog\Model\CommentType\Product')) {
            foreach ($response['response']['list'] as $key => $item) {
                $product = new Product($item['aid']);
                $this->addImageData($product);
                $response['response']['list'][$key]['product_object'] = Utils::extractOrm($product);
            }
        }

        return $response;
    }


    /**
     * Возвращает список комментариев
     *
     * @example GET /api/methods/comment.getList?filter[aid]=1
     *
     * Ответ:
     *
     * <pre>{
     *  "response": {
     *      "summary": {
     *      "page": 1,
     *      "pageSize": 1000,
     *      "total": "2"
     *      },
     *      "list": [
     *          {
     *              "id": "23",
     *              "type": "\\Catalog\\Model\\CommentType\\Product",
     *              "aid": "21196",
     *              "dateof": "2022-03-16 15:35:00",
     *              "user_id": "1",
     *              "user_name": "Иван",
     *              "message": "Все супер! Спасибо!",
     *              "moderated": null,
     *              "rate": "5",
     *              "help_yes": "0",
     *              "help_no": "0",
     *              "ip": "127.0.0.1",
     *              "useful": "0"
     *          },
     *          {
     *              "id": "24",
     *              "type": "\\Catalog\\Model\\CommentType\\Product",
     *              "aid": "21196",
     *              "dateof": "2022-03-16 15:37:08",
     *              "user_id": "2",
     *              "user_name": "Петр",
     *              "message": "Все плохо!",
     *              "moderated": null,
     *              "rate": "1",
     *              "help_yes": "0",
     *              "help_no": "0",
     *              "ip": "127.0.0.1",
     *              "useful": "0"
     *          }
     *       ]
     *    }
     * }
     * </pre>
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр комментариев по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка комментариев по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     *
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token = null, $filter = [], $sort = "id", $page = 1, $pageSize = 1000)
    {
        $response = parent::process($token, $filter, $sort, $page, $pageSize);

        if (
            isset($response['response']['summary'])
            && isset($filter['aid'])
            && isset($filter['type'])
        ) {
            $response['response']['summary']['mark_matrix'] = $this->dao->getMarkMatrix($filter['aid'], stripslashes($filter['type']));

            if ($filter['type'] == quotemeta('\Catalog\Model\CommentType\Product')) {
                $product = new Product($filter['aid']);
                $response['response']['summary']['product_rating'] = $product->rating;
            }
        }
        $this->addProductsData($response, $filter['type']);
        return $response;
    }
}
