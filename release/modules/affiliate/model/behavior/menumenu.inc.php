<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Model\Behavior;

use Affiliate\Model\Orm\Affiliate;
use RS\Behavior\BehaviorAbstract;

/**
 * Расширяет объект статьи
 */
class MenuMenu extends BehaviorAbstract
{
    /**
     * Возвращает связанный филиал для данного склада
     * @return Affiliate
     */
    function getAffiliate()
    {
        return new Affiliate($this->owner->affiliate_id);
    }
}