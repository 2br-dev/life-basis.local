<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * Dao объект для работы со списками промптов
 */
class PromptApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Prompt(), [
            'sortField' => 'sortn',
            'multisite' => true,
            'defaultOrder' => 'sortn'
        ]);
    }
}