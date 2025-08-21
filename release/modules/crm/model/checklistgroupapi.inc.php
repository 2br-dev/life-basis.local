<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\Orm\ChecklistGroup;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

/**
 * API для работы с группами чек-листов
 */
class CheckListGroupApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\ChecklistGroup());
    }

    /**
     * Получает список групп с чек-листами для указанной задачи
     *
     * @param int $taskId
     * @return array
     */
    public static function getFullChecklistByTaskId(int $taskId): array
    {
        $groups = Request::make()
            ->from(new ChecklistGroup())
            ->where(['task_id' => $taskId])
            ->objects('\Crm\Model\Orm\ChecklistGroup', 'uniq');

        $result = [];

        foreach ($groups as $group_uniq => $group) {
            $group_data = [
                'title' => $group['title'],
                'items' => [],
            ];

            foreach ($group['items'] as $item_uniq => $item) {
                $group_data['items'][$item_uniq] = [
                    'title' => $item['title'],
                    'is_done' => $item['is_done'],
                    'entity_type' => $item['entity_type'],
                    'entity_id' => $item['entity_id'],
                ];
            }

            $result[$group_uniq] = $group_data;
        }

        return $result;
    }
}