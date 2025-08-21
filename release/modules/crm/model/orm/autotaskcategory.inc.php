<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;
use RS\Orm\OrmObject;
use \RS\Orm\Type;

class AutotaskCategory extends OrmObject
{
    protected static
        $table = 'crm_autotask_category';
    
    function _init()
    {
        parent::_init()->append([
            'title' => new Type\Varchar([
                'description' => t('Название')
            ]),
        ]);
    }
}