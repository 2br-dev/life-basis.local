<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\CsvSchema;

use Ai\Model\StatisticApi;
use RS\Csv\AbstractSchema;
use RS\Csv\Preset;
use Ai\Model\Orm;

/**
 * Схема импорта/экспорта промптов в CSV
 */
class Statistic extends AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new Orm\Statistic(),
            'excludeFields' => ['id'],
            'savedRequest' => StatisticApi::getSavedRequest('Ai\Controller\Admin\StatisticCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
        ]));
    }
}