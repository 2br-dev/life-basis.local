<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use Ai\Model\Orm\Task;
use Ai\Model\Orm\TaskResult;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

/**
 * PHP API для работы
 */
class TaskResultApi extends EntityList
{
    const TIMEOUT = 20;

    function __construct()
    {
        parent::__construct(new Orm\TaskResult(), [
            'multisite' => true
        ]);
    }

    /**
     * Отправляет на повторную генерацию некоторые объекты
     *
     * @param $ids
     * @return bool
     */
    public function regenerate($ids)
    {
        if ($ids) {
            $task_ids = Request::make()
                ->select('DISTINCT task_id')
                ->from($this->getElement())
                ->whereIn('id', $ids)
                ->exec()
                ->fetchSelected(null, 'task_id');

            if ($task_ids) {
                Request::make()
                    ->update($this->getElement())
                    ->set([
                        'status' => TaskResult::STATUS_NEW,
                        '_generated_data' => null,
                        'error' => ''
                    ])
                    ->whereIn('id', $ids)
                    ->exec();

                Request::make()
                    ->update(new Task())
                    ->set([
                        'status' => Task::STATUS_NEW
                    ])
                    ->whereIn('id', $task_ids)
                    ->exec();
            }
        }

        return true;
    }

    /**
     * Отменяет выбранные результаты генерации
     *
     * @param array $ids список ID
     * @param integer $offset отступ с самого начала
     * @return integer|bool
     */
    public function apply($ids, $offset = 0)
    {
        $start = time();
        foreach(array_values($ids) as $n => $id) {
            if ($n < $offset) continue;

            $task_result = new TaskResult($id);
            if ($task_result->canApprove()) {
                $task_result->approve($task_result['generated_data']);
            }

            if ((time() - $start) > self::TIMEOUT) {
                return $n + 1;
            }
        }

        return true;
    }

    /**
     * Отменяет выбранные результаты генерации
     *
     * @param array $ids список ID
     * @param integer $offset отступ с самого начала
     * @return integer|bool
     */
    public function cancel($ids, $offset = 0)
    {
        $start = time();
        foreach(array_values($ids) as $n => $id) {
            if ($n < $offset) continue;

            $task_result = new TaskResult($id);
            if ($task_result->canCancel()) {
                $task_result->cancel();
            }

            if ((time() - $start) >= self::TIMEOUT) {
                return $n + 1;
            }
        }

        return true;
    }
}