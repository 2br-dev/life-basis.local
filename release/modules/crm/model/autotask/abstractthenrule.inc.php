<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Crm\Model\Autotask;

use RS\Event\Manager;

abstract class AbstractThenRule
{
    protected $operation;
    protected $vars;

    /**
     * Возвращает идентификатор класса действия
     *
     * @return string
     */
    abstract public function getId();

    /**
     * Возвращает публичное название класса действия
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает описание класса действия
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Возвращает объект поля для параметров
     *
     * @return mixed
     */
    abstract public function getPropertyIteratorField($field, $action, $params_type, $value = '');

    /**
     * Возвращает операцию над объектом
     *
     * @return mixed
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Устанавливает значение операции над объектом
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * Возвращает переменные действия
     *
     * @return mixed
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Устанавливает переменные действия
     */
    public function setVars($entity, $values)
    {
        $this->vars = $this->replaceVars($entity, $values);
    }

    /**
     * Возвращает действия, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getActions($if_rule = null)
    {
        $actions = [
            'create' => t('Создать'),
            'update' => t('Изменить'),
        ];

        if ($if_rule) {
            $availableActions = $if_rule->getAvailableActions();

            $actions = array_filter($actions, function($key) use ($availableActions) {
                return in_array($key, $availableActions);
            }, ARRAY_FILTER_USE_KEY);
        }

        return ['' => t('Выберите действие')] + $actions;
    }

    /**
     * Возвращает массив доступных параметров для действия
     *
     * @return array
     */
    public function getParams()
    {
        return [];
    }

    /**
     * Возвращает массив доступных параметров для действия
     *
     * @return array
     */
    public function getConditionParams()
    {
        return [];
    }

    /**
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [];
    }

    /**
     * Заменяет переменные в необходимых строках массива значений $values
     *
     * @param mixed $autotask_rule
     * @param array $string
     * @return mixed
     */
    public function replaceVars($autotask_rule, $values)
    {
        return $values;
    }

    /**
     * Возвращает все зарегистрированные в системе классы-условия
     *
     * @return  AbstractIfRule[]
     */
    final public static function getAllThenRules()
    {
        $then_rules_objects = Manager::fire('crm.autotask.getthenrules', [])->getResult();
        foreach($then_rules_objects as $item) {
            if (!($item instanceof self)) {
                throw new \RS\Exception(t('Класс-условие для автозадач должен был потомком класса Crm\Model\AutoTask\AbstractThenRule'));
            }
        }

        return $then_rules_objects;
    }

    /**
     * Возвращает путь к шаблонам, используемым в классе-условии
     *
     * @return string
     */
    public function getTplFolder()
    {
        return \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.\RS\Module\Item::nameByObject($this).\Setup::$MODULE_TPL_FOLDER;
    }

    /**
     * Возвращает объект класса-действия по его идентификатору
     *
     * @param string $id
     */
    public static function getClassById($id)
    {
        $rules = self::getAllThenRules();
        foreach($rules as $rule) {
            if ($rule->getId() == $id) {
                return $rule;
            }
        }
        throw new \RS\Exception(t('Класс типа связи `%0` не найден', [$id]));
    }

    /**
     * Запускает выполнение действия
     *
     * @return void
     */
    public static function run($autotask)
    {
        $rule_then = $autotask->getThenRule();
        $methodName = 'run' . ucfirst($rule_then->getOperation());

        if (method_exists($rule_then, $methodName)) {
            $rule_then->$methodName($autotask);
        }
    }
}
