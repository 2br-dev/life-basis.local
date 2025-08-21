<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Filter;

use Catalog\Model\Orm\Offer;
use RS\Html\Filter\Type\AbstractType;
use RS\Orm\Request;

/**
 * Фильтр по штрихкоду товаров в административной панели.
 */
class SkuFilter extends AbstractType
{
    public
        $tpl = '%catalog%/filter/sku_filter.tpl';

    /**
     * Модифицирует запрос на выборку товаров для фильтрации элементов
     *
     * @param Request $q
     * @return Request
     * @throws \RS\Exception
     */
    function modificateQuery(Request $q)
    {
        if ($this->getValue() != '') {
            $q->leftjoin(new Offer(), 'SKU_OFFER.product_id = A.id', 'SKU_OFFER');
            $q->where("({$this->getSqlKey()} like '%{$this->escape($this->getValue())}%' OR SKU_OFFER.sku like '%{$this->escape($this->getValue())}%')");
            parent::modificateQuery($q);
        }
        return $q;
    }

    /**
     * Возвращает условие для выборки для секции WHERE
     *
     * @return string
     */
    function getWhere()
    {
        return '';
    }
}

