<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use RS\Event\Manager;

abstract class AbstractIfRule
{
    const MODE_EVENT = 'event'; // для ORM-событий
    const MODE_CRON = 'cron';   // для задач по расписанию

    protected $entity;
    protected $operation;
    protected $rule;

    protected static string $mode = self::MODE_EVENT;

    /**
     * @var AbstractThenRule
     */
    protected $then_rule;

    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    abstract public function getId();

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает массив действий над объектом по типу
     *
     * @return array
     */
    abstract public function getOperationsByType($type);

    /**
     * Возвращает все зарегистрированные в системе классы-условия
     *
     * @return  AbstractIfRule[]
     */
    final public static function getAllIfRules()
    {
        $if_rules_objects = Manager::fire('crm.autotask.getifrules', [])->getResult();
        foreach($if_rules_objects as $item) {
            if (!($item instanceof self)) {
                throw new \RS\Exception(t('Класс-условие для автозадач должен был потомком класса Crm\Model\AutoTask\AbstractIfRule'));
            }
        }

        return $if_rules_objects;
    }

    /**
     * Возвращает объекта взаимодействия
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

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
     * Возвращает объект автозадачи
     *
     * @return mixed
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Возвращает объект действия
     *
     * @return mixed
     */
    public function getThenRule()
    {
        return $this->then_rule;
    }

    /**
     * Возвращает действия, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getActions()
    {
        return [
            '' => t('Выберите действие'),
        ];
    }

    /**
     * Возвращает дополнительные параметры, которые будут учитываться при выполнении условия
     *
     * @param $action
     * @return array
     */
    public function getParams($action = null)
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
     * Возвращает значения переменных, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceValues($entity)
    {
        return [];
    }

    /**
     * Возвращает тип связи для объекта
     *
     * @return null
     */
    public function getLinkedTypeId()
    {
        return null;
    }

    /**
     * Воздращает доступные для условия действия
     *
     * @return array
     */
    public function getAvailableActions()
    {
        return ['create', 'update'];
    }

    /**
     * Возвращает значения параметра
     *
     * @param string $param
     * @return array
     */
    public function getParamValues($param)
    {
        $methodName = 'get' . ucfirst(str_replace('_', '', ucwords($param, '_')));
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        return [];
    }

    /**
     * Возвращает тип поля для шаблона
     *
     * @return string
     */
    public function getNodeType($key)
    {
        return 'select';
    }

    /**
     * Возвращает true, если параметр является множественным
     *
     * @return bool
     */
    public function isMultiple($key)
    {
        return false;
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
     * Возвращает массив из ID классов-условий по режиму работы
     *
     * @param string $mode
     * @return array
     * @throws \RS\Exception
     */
    public static function getClassesByMode($mode)
    {
        $result = [];
        $allRules = self::getAllIfRules();

        foreach ($allRules as $rule) {
            if ($rule::getMode() === $mode) {
                $result[] = $rule->getId();
            }
        }

        return $result;
    }

    /**
     * Возвращает режим работы класса-условия
     *
     * @return string
     */
    public static function getMode()
    {
        return static::$mode ?? self::MODE_EVENT;
    }

    /**
     * Возвращает объект класса-условия по его идентификатору
     *
     * @param string $id
     */
    public static function getClassById($id)
    {
        $rules = self::getAllIfRules();
        foreach($rules as $rule) {
            if ($rule->getId() == $id) {
                return $rule;
            }
        }
        throw new \RS\Exception(t('Класс типа связи `%0` не найден', [$id]));
    }

    /**
     * Возвращает список поддерживаемых событий для класса-условия
     *
     * @return array
     */
    public static function getSupportsEvent()
    {
        $instance = new static();
        return [$instance->getId()];
    }

    /**
     * Инициализирует объект действия
     * Дополняет объект действия необходимыми данными
     *
     * @return void
     * @throws \RS\Exception
     */
    public function initThenRule()
    {
        $this->then_rule = AbstractThenRule::getClassById($this->rule['then_type']);
        $this->then_rule->setOperation($this->rule['then_action']);
        $this->then_rule->setVars($this, $this->rule['then_params_arr']);

    }

    /**
     * Модифицирует значение условия, если требуется
     *
     * @param $item
     * @return mixed $item
     */
    protected function modifyParamsItem($item)
    {
        return $item;
    }

    /**
     * Рассчитывает хэш для всех значений полей из условия для объекта объекта
     *
     * @param $object
     * @param $values
     * @return string
     */
    protected function calculateObjectHash($object, $values)
    {
        ksort($values);

        $extractedValues = [];

        foreach ($values as $value) {
            if (isset($value['key'])) {
                $extractedValues[] = $object[$value['key']];
            }
        }
        $stringToHash = implode('', $extractedValues);

        return md5($stringToHash);
    }

    /**
     * Проверяет соответствие параметров автозадачи у объекта взаимодействия
     *
     * @return bool
     */
    public function compareParams()
    {
        $before_object = null;
        if (method_exists($this->entity, 'getBeforeObject')) {
            $before_object = $this->entity->getBeforeObject();
        }
        if (!in_array($this->rule['if_action'], $this->getOperationsByType($this->operation))) {
            return false;
        }
        foreach ($this->rule['if_params_arr'] as $item) {
            $item = $this->modifyParamsItem($item);

            if (!isset($this->entity[$item['key']]) || $this->entity[$item['key']] != $item['value']) {
                return false;
            }
            if ($before_object &&
                $this->calculateObjectHash($before_object, $this->rule['if_params_arr']) ==
                $this->calculateObjectHash($this->entity, $this->rule['if_params_arr'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Проверяет возможность запуска автозадачи
     * Наполняет объект условия данными об объекте взаимодействия, об объекте автозадачи, об объекте действия
     *
     * @param $item
     * @param $params
     * @return AbstractIfRule|false
     * @throws \RS\Exception
     */
    public static function match($item, $if_class, $params)
    {
        $if_class->entity = $params['orm'] ?? null;
        $if_class->operation = $params['flag'] ?? null;
        $if_class->rule = $item;

        if ($if_class->compareParams()) {
            $if_class->initThenRule();
            return $if_class;
        }
        return false;
    }
}
