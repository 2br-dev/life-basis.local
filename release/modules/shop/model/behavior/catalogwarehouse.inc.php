<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Behavior;

use RS\Behavior\BehaviorAbstract;
use Shop\Model\Orm\Region;

/**
 * Расширяет объект Warehouse в модуле Catalog
 */
class CatalogWarehouse extends BehaviorAbstract
{
    /**
     * Возвращает связанный со складом регион
     *
     * @return Region
     */
    function getLinkedRegion()
    {
        return new Region($this->owner['linked_region_id']);
    }
}