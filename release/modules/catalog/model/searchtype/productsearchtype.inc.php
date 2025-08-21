<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\SearchType;

use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use RS\Config\Loader;
use RS\Db\Adapter as DbAdapter;
use RS\Orm\Request;
use Search\Model\SearchType\AbstractSearchType;

/**
 * Тип поиска - по товарам
 */
class ProductSearchType extends AbstractSearchType
{
    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска.
     * Ищет по точному совпадению артикула товар
     *
     * @param Request $q объект запроса
     * @param string $alias_product псевдоним таблицы с товарами
     * @param string $alias псевдоним таблицы с поисковыми данными
     * @return mixed
     */
    public function afterJoinQuery(Request $q, $alias_product = 'A', $alias = 'B')
    {
        $query = $this->search_engine->getQuery();

        $config = Loader::byModule($this);
        $expression_values = [
            'query' => $query
        ];

        if ($config['search_barcode_like']) {
            $expression = "barcode LIKE '%#query%' OR sku = '#query'";
        } else {
            $expression = "barcode = '#query' OR sku = '#query'";
        }

        //Если введено одно слово, значит возможно это артикул,
        //добавляем условие поиска по точному совпадению артикула
        if (strpos($query, ' ') === false) {
            $products_id = Request::make()
                ->select('id')
                ->from(Product::_getTable())
                ->where($expression, $expression_values)
                ->exec()->fetchSelected(null, 'id');

            if (!$products_id) {
                $products_id = Request::make()
                    ->select('product_id')
                    ->from(Offer::_getTable())
                    ->where($expression, $expression_values)
                    ->exec()->fetchSelected(null, 'product_id');
            }

            if ($products_id) {
                $q->whereIn("$alias_product.id", $products_id, 'OR');
            }
        }
    }
}