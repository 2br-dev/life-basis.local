<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\CsvSchema;

use RS\Csv\AbstractSchema;
use RS\Csv\Preset;
use Ai\Model\Orm;

/**
 * Схема импорта/экспорта промптов в CSV
 */
class Prompt extends AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new Orm\Prompt(),
            'excludeFields' => ['id', 'site_id'],
            'multisite' => true,
            'savedRequest' => \Ai\Model\PromptApi::getSavedRequest('Ai\Controller\Admin\PromptCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'searchFields' => ['transformer_id', 'field', 'note']
        ]));
    }
}