<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\CsvSchema;

use Crm\Model\CsvPreset\LinksReverse;
use Crm\Model\Orm\Interaction;
use Crm\Model\Orm\Task;
use \RS\Csv\Preset;
use RS\Config\Loader;
use Crm\Model\Orm\Status;
use Crm\Model\CsvPreset\CustomFields;
use Crm\Model\CsvPreset\Links;

/**
 * Схема экспорта/импорта типов задач в CSV
 */
class TaskType extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Crm\Model\Orm\TaskType(),
            'excludeFields' => [
                'id', '_tmpid'
            ],
            'savedRequest' => \Crm\Model\TaskTypeApi::getSavedRequest('Crm\Controller\Admin\TaskType_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'searchFields' => ['title']
        ]));
    }
}