<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Statistic\Config;

use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\ConfigObject;
use RS\Orm\Type;

class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'consider_orders_status' => new Type\ArrayList([
                'runtime' => false,
                'description' => t('Учитывать в отчетах заказы в следующих статусах (удерживая CTRL можно выбрать несколько)'),
                'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, ['0' => t('- все статусы -')]],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]],
            ])
        ]);
    }
}
