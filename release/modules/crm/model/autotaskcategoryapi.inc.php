<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\Orm\AutotaskCategory;
use RS\Module\AbstractModel\EntityList;

class AutotaskCategoryApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new AutotaskCategory(), [
            'nameField' => 'title',
        ]);
    }
}
