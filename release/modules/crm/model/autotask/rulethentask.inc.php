<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use Crm\Model\BoardApi;
use Crm\Model\Orm\Task;
use Crm\Model\TaskApi;
use Crm\Model\Utils;
use RS\Config\Loader;
use RS\Orm\FormObject;
use RS\Orm\Request;
use RS\Orm\Type;
use Users\Model\Orm\User;
use Users\Model\Orm\UserInGroup;

class RuleThenTask extends AbstractThenRule

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
        return t('Задачу');
    }

    /**
     * Возвращает описание класса
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Операции с задачей');
    }

    /**
     * Возвращает объект поля для параметров
     *
     * @param $field
     * @param $action
     * @param $value
     * @return mixed
     */
    public function getPropertyIteratorField($field, $action, $params_type, $value = '')
    {
        $form = new FormObject((new Task)->getPropertyIterator());
        $fields = [];
        $fieldName = 'then_params_arr[' . $params_type . '][' . $field . '][value]';
        switch ($field) {
            case 'level':
                $level_values = ($action == 'create')
                    ? ['current' => t('Текущий уровень'), 'child' => t('Дочерний')]
                    : ['parent' => t('Родительский'), 'child' => t('Дочерний'), 'root' => t('Основная задача')];

                $fields[$field] = new Type\Varchar([
                    'description'   => t('Уровень вложенности'),
                    'ListFromArray' => [$level_values],
                    'runtime'       => true,
                ]);
                break;

            case 'identificator':
                $fields[$field] = new Type\Varchar([
                    'description' => t('Идентификатор'),
                ]);
                break;

            case 'date_of_planned_end':
                $fields[$field] = new Type\Varchar([
                    'description' => t('Планируемая дата завершения'),
                    'hint'        => t('d - дней, h - часов, m - минут, s - секунд. Например: 1d 4h 2m 1s'),
                ]);
                break;
            case 'observer_users_id':
            case 'collaborator_users_id':
                $fieldName .= '[]';
                break;
            case 'random_implementer_user_id':
                $fields[$field] = new Type\Varchar([
                    'default' => '',
                    'list' => [['\Users\Model\GroupApi', 'staticSelectList'], ['' => t('Не задано')]]
                ]);
                break;
            case 'root_implementer_user_id':
                $fields[$field] = new Type\Integer([
                    'checkboxView' => [1,0]
                ]);
                break;
            case (str_starts_with($field, 'custom_fields[')):
                $alias = substr($field, strlen('custom_fields['), -1);

                $config = Loader::byModule($this);
                $user_field_manager = $config->getTaskUserFieldsManager()->setArrayWrapper('custom_fields');

                if ($field_info = $user_field_manager->getStructure()) {
                    if (isset($field_info[$alias])) {
                        $type = $field_info[$alias]['type'];
                        $title = $field_info[$alias]['title'];
                        $values = isset($field_info[$alias]['values']) ? explode(',', $field_info[$alias]['values']) : [];
                        switch ($type) {
                            case 'string':
                                $fields[$field] = new Type\Varchar([
                                    'description' => $title,
                                ]);
                                break;

                            case 'text':
                                $fields[$field] = new Type\Text([
                                    'description' => $title,
                                ]);
                                break;

                            case 'list':
                                $fields[$field] = new Type\Varchar([
                                    'description' => $title,
                                    'ListFromArray' => [$values]
                                ]);
                                break;

                            case 'bool':
                                $fields[$field] = new Type\Integer([
                                    'description' => $title,
                                    'checkboxView' => [1, 0]
                                ]);
                                break;
                        }
                    }
                }

                $fieldName = 'then_params_arr[' . $params_type . '][custom_fields][' . $alias . '][value]';
                break;
        }

        if (!empty($fields)) {
            $form->getPropertyIterator()->append($fields);
        }

        $form->getPropertyIterator()[$field]->setFormName($fieldName);
        $form[$field] = $value;
        return $form['__' . $field];
    }

    /**
     * Возвращает массив доступных параметров для действия
     *
     * @return array
     */
    public function getParams()
    {
        $fields = [
            'level' => t('Уровень вложенности'),
            'title' => t('Суть задачи'),
            'status_id' => t('Статус'),
            'description' => t('Описание'),
            'expiration_notice_time' => t('Уведомить исполнителя о скором истечении срока выполнении задачи за...'),
            'is_archived' => t('Задача архивная?'),
            'date_of_planned_end' => t('Планируемая дата завершения'),
            'creator_user_id' => t('Создатель задачи'),
            'implementer_user_id' => t('Исполнитель задачи'),
            'random_implementer_user_id' => t('Исполнитель задачи (случайный из группы)'),
            'root_implementer_user_id' => t('Исполнитель задачи (менеджер из объекта условия)'),
            'collaborator_users_id' => t('Соисполнители'),
            'observer_users_id' => t('Наблюдатели'),
        ];
        $config = Loader::byModule($this);
        $user_field_manager = $config
            ->getTaskUserFieldsManager()
            ->setArrayWrapper('custom_fields');
        if ($user_field_manager->notEmpty()) {
            foreach($user_field_manager->getStructure() as $item) {
                $fields['custom_fields['.$item['alias'].']'] = t('Дополнительное поле `%0`', [$item['title']]);
            }
        }
        return $fields;
    }

    /**
     * Возвращает массив доступных параметров для выборки объекта у действия
     *
     * @return array
     */
    public function getConditionParams()
    {
        return parent::getParams() + [
                'type_id' => t('Тип задачи'),
                'identificator' => t('Идентификатор'),
                'level' => t('Уровень вложенности'),
            ];
    }

    /**
     * Заменяет переменные в необходимых строках массива значений $values
     *
     * @param AbstractIfRule $autotask_rule
     * @param array $string
     * @return mixed
     */
    public function replaceVars($autotask_rule, $values)
    {
        $entity = $autotask_rule->getEntity();
        $rule = $autotask_rule->getRule();

        $result = ['params' => []];

        if (isset($rule['then_params_arr']['params']['level'])) {
            if ($rule['then_params_arr']['params']['level']['value'] == 'child') {
                $result['params']['parent'] = $entity['id'];
            }
            if ($rule['then_params_arr']['params']['level']['value'] == 'current') {
                $result['params']['parent'] = $entity['parent'];
            }
            unset($values['params']['level']);
        }

        if (isset($rule['then_params_arr']['params']['date_of_planned_end'])) {
            $delta = Utils::getDurationDeltaTimestamp($rule['then_params_arr']['params']['date_of_planned_end']['value']);
            $result['params']['date_of_planned_end'] = date('Y-m-d H:i:s', time() + $delta);
            unset($values['params']['date_of_planned_end']);
        }

        $replace_values = $autotask_rule->getReplaceValues($entity);

        if (isset($values['conditions'])) {
            foreach ($values['conditions'] as $item) {
                $result['conditions'][$item['key']] = $item['value'];
            }
        }else {
            $result['params']['autotask_identificator'] = $rule['identificator'];
        }

        foreach ($values['params'] as $key => $values) {
            if ($key != 'custom_fields') {
                $result['params'][$values['key']] = str_replace(array_keys($replace_values), array_values($replace_values), $values['value']);
            }else {
                $result['params'][$key] = [];
                if ($values) {
                    foreach ($values as $alias => $value) {
                        $result['params'][$key][$alias] = $value['value'];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает ID одного пользователя из группы
     *
     * @param $group_id
     * @return int|string|null
     */
    public function getRandomUserFromGroup($group_id)
    {
        $user_ids = Request::make()
            ->select('U.*')
            ->from(new User(), 'U')
            ->join(new UserInGroup(), 'G.user = U.id', 'G')
            ->where([
                'G.group' => $group_id,
            ])
            ->orderby('surname asc, name asc, midname asc')
            ->objects(null, 'id');

        if ($user_ids) {
            $ids = array_keys($user_ids);
            return $ids[rand(0, count($ids) - 1)];
        }
        return null;
    }

    /**
     * Устанавливает динамические параметры
     *
     * @param $params
     * @return mixed
     */
    public function setDynamicParameters($autotask)
    {
        $params = $this->vars['params'];
        if (isset($params['root_implementer_user_id']) && $params['root_implementer_user_id']) {
            $orm = $autotask->getEntity();
            if (method_exists($orm, 'getManagerUser') && $manager_user = $orm->getManagerUser()) {
                if ($manager_user['id']) {
                    $params['implementer_user_id'] = $manager_user->id;
                }
            }
        }

        if (isset($params['random_implementer_user_id']) && $params['random_implementer_user_id']) {
            if ($user_id = $this->getRandomUserFromGroup($params['random_implementer_user_id'])) {
                $params['implementer_user_id'] = $user_id;
            }
        }

        unset($params['random_implementer_user_id']);
        unset($params['root_implementer_user_id']);
        return $params;
    }

    /**
     * Создание автозадачи
     *
     * @param $autotask
     * @return void
     */
    public function runCreate($autotask)
    {
        $task = new Task();
        $task->getFromArray($this->setDynamicParameters($autotask));
        $task['task_num'] = \RS\Helper\Tools::generatePassword(8, range('0', '9'));
        $task['date_of_create'] = date('Y-m-d H:i:s');
        if (method_exists($autotask, 'getRootId')) {
            $task['autotask_root_id'] = $autotask->getRootId();
        }
        if ($linked_type_id = $autotask->getLinkedTypeId()) {
            $task['links'] = [
                $linked_type_id => [$autotask->getEntity()->id]
            ];
        }
        $task['create_by_rule_id'] = $autotask->getRule()->id;
        if ($task->insert()) {
            BoardApi::moveToFirst(new TaskApi(), $task);
        }
    }

    /**
     * Изменение задачи по событию
     *
     * @return void
     */
    public function runUpdate($autotask)
    {
        if (isset($this->vars['conditions'])) {
            $request_params = [];
            foreach ($this->vars['conditions'] as $key => $value) {
                if ($key === "identificator") {
                    $request_params['autotask_identificator'] = $value;
                } else if ($key === "level") {
                    $entity = $autotask->getEntity();
                    if ($value == 'parent') {
                        $request_params['id'] = $entity['parent'];
                    }elseif ($value == 'child') {
                        $request_params['parent'] = $entity['id'];
                    }elseif ($value == 'root') {
                        $request_params['id'] = $entity['autotask_root_id'];
                    }
                } else {
                    $request_params[$key] = $value;
                }
            }
            if ($request_params) {
                if ($tasks = Request::make()
                    ->from(new Task())
                    ->where($request_params)
                    ->objects('Crm\Model\Orm\Task')) {

                    foreach ($tasks as $task) {
                        $task->getFromArray($this->vars['params']);
                        $task['create_by_rule_id'] = $autotask->getRule()->id;
                        if ($task->update()) {
                            BoardApi::moveToFirst(new TaskApi(), $task);
                        }
                    }
                }
            }
        }
    }
}