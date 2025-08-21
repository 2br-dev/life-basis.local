<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;

use RS\Module\AbstractModel\EntityList;

class VerificationSessionApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\VerificationSession(), [
            'idField' => 'uniq',
        ]);
    }
}