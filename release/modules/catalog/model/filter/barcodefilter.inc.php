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

/**
 * Фильтр по артикулу в административной панели.
 */
class BarcodeFilter extends AbstractType
{
    public
        $tpl = 'system/admin/html_elements/filter/type/string.tpl';

    function modificateQuery(\RS\Orm\Request $q)
    {
        if ($this->getValue() != '') {
            $q->leftjoin(new Offer(), 'BARCODE_OFFER.product_id = A.id', 'BARCODE_OFFER');
            $q->where("({$this->getSqlKey()} like '%{$this->escape($this->getValue())}%' OR BARCODE_OFFER.barcode like '%{$this->escape($this->getValue())}%')");
            parent::modificateQuery($q);
        }
        return $q;
    }

    function getWhere()
    {
        return '';
    }
}

