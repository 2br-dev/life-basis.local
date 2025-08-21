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
use RS\Orm\Request as OrmRequest;

class RuleIfTask extends AbstractIfRule
{
    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'task';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Задача');
    }

    /**
     * Возвращает массив действий над объектом по типу
     *
     * @return array
     */
    public function getOperationsByType($type)
    {
        if ($type == 'insert') {
            return ['create'];
        }
        if ($type == 'update') {
            return ['change_status'];
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
            'create' => t('Создан'),
            'change_status' => t('Сменил статус'),
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
            'type_id' => t('Тип'),
            'status_id' => t('Статус'),
            'identificator' => t('Идентификатор'),
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
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [
            'task_num' => t('Номер задачи'),
            'title' => t('Суть задачи'),
            'description' => t('Описание'),
        ];
    }

    /**
     * Возвращает значения переменных, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceValues($entity)
    {
        return[
            '{task_num}' => $entity['task_num'],
            '{title}' => $entity['title'],
            '{description}' => $entity['description'],
        ];
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
     * Возвращает список типов задач
     *
     * @return array
     */
    public function getTypeId()
    {
        return TaskTypeApi::staticSelectList();
    }

    /**
     * Возвращает идентификатор корневой задач
     *
     * @return array
     */
    public function getRootId()
    {
        $task = $this->entity;

        while (isset($task['parent']) && $task['parent'] != 0) {
            $parentTask = OrmRequest::make()
                ->from(new Task())
                ->where(['id' => $task['parent']])
                ->object('Crm\Model\Orm\Task');

            if (!$parentTask) {
                break;
            }
            $task = $parentTask;
        }
        return $task['id'];
    }
}