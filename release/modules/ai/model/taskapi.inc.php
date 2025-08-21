<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use Ai\Model\Orm\Task;
use RS\Module\AbstractModel\EntityList;

/**
 * PHP API для работы с задачами на генерацию данных
 */
class TaskApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Task(), [
            'multisite' => true,
            'defaultOrder' => 'id',
        ]);
    }

    /**
     * Запускает один шаг генерации данных
     *
     * @param integer $timeout_sec Максимальный лимит выполнения в секундах.
     * Метод приостановит генерацию и продолжит ее, при следующем запуске
     *
     * @return integer Возвращает количество объектов, для которых сгенерированы данные
     */
    public function runGenerationStep($timeout_sec)
    {
        $result = 0;
        $start = time();
        foreach($this->getTaskForGeneration() as $task) {
            if ($task['status'] == Task::STATUS_NEW) {
                $task['status'] = Task::STATUS_GENERATING;
                $task->update();
            }

            foreach($task->getTaskResultForGeneration() as $task_result) {
                $task_result->generate();
                if ((time() - $start) > $timeout_sec) break 2;
            }

            if ($task['total_count'] == $task['generated_count']) {
                $task['status'] = Task::STATUS_READY;
                $task->update();
            }
        }

        return $result;
    }

    /**
     * Возвращает список задач для генерации данных
     *
     * @return array
     */
    protected function getTaskForGeneration()
    {
        $api = new self();
        $api->setFilter('status', [
            Task::STATUS_NEW,
            Task::STATUS_GENERATING
        ], 'in');

        return $api->getList();
    }
}