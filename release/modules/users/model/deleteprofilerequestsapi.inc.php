<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;

use RS\Module\AbstractModel\EntityList;
use Users\Model\Orm\DeleteProfileRequests;

class DeleteProfileRequestsApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new DeleteProfileRequests(), ['multisite' => true]);
    }
}