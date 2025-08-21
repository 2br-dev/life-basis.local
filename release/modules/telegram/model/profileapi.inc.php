<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * PHP Api для работы с профилями Telegram
 */
class ProfileApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Profile(), [
            'multisite' => true,
            'nameField' => 'title',
            'LoadOnDelete' => true
        ]);
    }
}