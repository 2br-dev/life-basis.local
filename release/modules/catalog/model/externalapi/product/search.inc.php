<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;
use Catalog\Config\File;
use Catalog\Model\SearchLineApi;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Utils;
use RS\Config\Loader;
use RS\Config\Loader as ConfigLoader;

/**
* Возвращает товар по ID
*/
class Search extends AbstractMethod
{
    /**
     * Живой поиск по товарам/брендам/категориям
     *
     * @param string $query Строка поиска
     * @param string $order_field Поле сортировки результата, возможные значения:
     * <b>relevant</b> - Релевантность
     * <b>sortn</b> - Вес
     * <b>dateof</b> - Дата
     * <b>rating</b> - Рейтинг
     * <b>cost</b> - Цена
     * <b>title</b> - Название
     * @param string $order_direction Направление сортировки, возможные значения:
     * <b>asc</b> - по возрастанию
     * <b>desc</b> - по убыванию
     * @param integer $search_limit Количество товаров в результате
     * @param integer $search_brand_limit Количество брендов в результате
     * @param integer $search_category_limit Количество категорий в результате
     * @param string $token Авторизационный токен
     *
     * @example GET api/methods/product.search?query=Смартфон
     * Ответ:
     * <pre>
     * "response": {
     *      "products": {
     *          "1": {
     *              "id": "1",
     *              "title": "Смартфон 1"
     *          },
     *          "2": {
     *              "id": "2",
     *              "title": "Смартфон 2"
     *          },
     *          ...
     *      },
     *      "categories": {
     *          "1": {
     *              "id": "1",
     *              "title": "Смартфоны"
     *          }
     *      }
     * }
     * </pre>
     *
     * @return array Возвращает массив ID и названия сущности
     */
    function process($query, $order_field = 'relevant', $order_direction = 'asc', $search_limit = 5, $search_brand_limit = 1, $search_category_limit = 1, $token = null)
    {
        $search_line_api = new SearchLineApi();
        $response['response'] = [];
        $config = File::config();

        if (!empty($query)) {

            $search_line_api->prepareSearchQueryProduct($query, $this, $order_field, $order_direction);
            $list = $search_line_api->getSearchQueryProductResults($search_limit);

            if (!empty($list)) {
                foreach ($list as $product) {
                    if ($config['hide_unobtainable_goods'] != 'Y' || $product->getNum() > 0) {
                        $result_product = ['id' => $product->id, 'title' => $product->title];
                        $images = [];
                        foreach ($product->getImages() as $image) {
                            $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
                        }
                        $result_product['image'] = $images;

                        $response['response']['products'][$product->id] = $result_product;
                    }
                }
            }

            if ($search_brand_limit) {
                $list = $search_line_api->getSearchQueryBrandsResults($query, $search_brand_limit);
                if (!empty($list)) {
                    foreach ($list as $brand) {
                        $result_brand = ['id' => $brand->id, 'title' => $brand->title];
                        if ($brand['image']){
                            $result_brand['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($brand->__image);
                        }
                        $response['response']['brands'][$brand->id] = $result_brand;
                    }
                }
            }

            if ($search_category_limit) {
                $list = $search_line_api->getSearchQueryCategoryResults($query, $search_category_limit);
                if (!empty($list)) {
                    foreach ($list as $dir) {
                        $result_dir = ['id' => $dir->id, 'title' => $dir->name];
                        if ($dir['image']){
                            $result_dir['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($dir->__image);
                        }
                        $response['response']['categories'][$dir->id] = $result_dir;
                    }
                }
            }
        }

        return $response;
    }
}
