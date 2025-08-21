<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use Crm\Model\Orm\Task;
use Crm\Model\TaskTypeApi;
use RS\Orm\Request;
use RS\Orm\Request as OrmRequest;

class RuleIfTaskList extends AbstractIfRule
{
    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'task_list';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Группа задач');
    }

    /**
     * Возвращает массив действий над объектом по типу
     *
     * @return array
     */
    public function getOperationsByType($type)
    {
        if ($type == 'update') {
            return ['end'];
        }
        return [];
    }

    /**
     * Возвращает действия, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getActions()
    {
        return parent::getActions() + [
                'change_status' => t('Перешла в статус'),
            ];
    }

    /**
     * Возвращает дополнительные параметры, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getParams($action = null)
    {
        return [
            'status_id' => t('Статус'),
            'identificator' => t('Идентификаторы'),
        ];
    }

    /**
     * Возвращает тип поля для шаблона
     *
     * @return string
     */
    public function getNodeType($key)
    {
        $node_type = 'select';
        if ($key == 'identificator') {
            $node_type = 'input';
        }
        return $node_type;
    }

    /**
     * Возвращает true, если параметр является множественным
     *
     * @return bool
     */
    public function isMultiple($key)
    {
        return $key == 'identificator';
    }

    /**
     * Воздращает доступные для условия действия
     *
     * @return array
     */
    public function getAvailableActions()
    {
        return ['update'];
    }

    /**
     * Возвращает статусы
     *
     * @return array
     */
    public function getStatusId()
    {
        return \Crm\Model\Orm\Status::getStatusesTitles('crm-task');
    }

    /**
     * Модифицирует значение условия, если требуется
     *
     * @param $item
     * @return mixed $item
     */
    protected function modifyParamsItem($item)
    {
        if ($item['key'] == 'identificator') {
            $item['key'] = 'autotask_identificator';
        }
        return $item;
    }

    /**
     * Возвращает список поддерживаемых событий для класса-условия
     *
     * @return array
     */
    public static function getSupportsEvent()
    {
        return ['task'];
    }

    /**
     * Проверяет, пора ли выполнить автозадачу по текущим параметрам (день/время)
     *
     * @return bool
     */
    public function compareParams()
    {
        $params = [];

        foreach ($this->rule['if_params_arr'] as $item) {
            $item = $this->modifyParamsItem($item);
            $params[$item['key']] = [
                'key' => $item['key'],
                'value' => $item['value']
            ];
        }

        if ($this->entity['autotask_root_id']) {
            $q = Request::make()
                ->select('id', 'autotask_identificator')
                ->from(new Task())
                ->where(['autotask_root_id' => $this->entity['autotask_root_id']]);

            foreach ($params as $param) {
                if (is_array($param['value'])) {
                    $q->whereIn($param['key'], $param['value']);
                }else {
                    $q->where([$param['key'] => $param['value']]);
                }
            }

            $tasks = $q->exec()->fetchSelected('autotask_identificator', 'id', true);

            if (!$tasks || !isset($params['autotask_identificator']['value']) || !is_array($params['autotask_identificator']['value'])) {
                return false;
            }

            $expectedTasks = $params['autotask_identificator']['value'];
            $actualTasks = array_keys($tasks);

            sort($expectedTasks);
            sort($actualTasks);

            return $expectedTasks === $actualTasks;
        }
        return false;
    }
}