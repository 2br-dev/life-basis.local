<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\AccessControl;

use RS\Exception;
use RS\Exception as RSException;

/**
* Абстрактный объект прав модуля
*/
abstract class AbstractModuleRights
{
    protected static
        $instance = [];

    protected
        $module,
        $rights = [],
        $right_groups = [],
        $root_items,
        $auto_checkers;

    final protected function __construct($module)
    {
        $this->module = $module;

        $this->addRights($this->getSelfModuleRights());

        $event_name = 'module.getrights.' . $this->module;
        $additional_rights = \RS\Event\Manager::fire($event_name, [])->getResult();
        $this->addRights((array) $additional_rights);

        $event_name = 'module.getcheckers.' . $this->module;
        $all_checkers = \RS\Event\Manager::fire($event_name, $this->getSelfAutoCheckers())->getResult();
        foreach ($all_checkers as $checker) {
            $this->auto_checkers[$checker->getCheckerType()][] = $checker;
        }

    }

    /**
    * Возвращает экземпляр объекта
    *
    * @param \RS\Orm\ConfigObject $config - объект конфигурации модуля
    * @return static
    */
    final public static function getInstance(\RS\Orm\ConfigObject $config)
    {
        $module = \RS\Module\Item::nameByObject($config);
        if (!isset(self::$instance[$module])) {
            self::$instance[$module] = new static($module);
        }
        return self::$instance[$module];
    }

    /**
    * Возвращает собственные права
    *
    * @return (Right|RightGroup)[]
    */
    abstract protected function getSelfModuleRights();

    /**
    * Возвращает собственные инструкции для автоматических проверок
    *
    * @return \RS\AccessControl\AutoCheckers\AutoCheckerInterface[]
    */
    abstract protected function getSelfAutoCheckers();

    /**
    * Добавляет права
    *
    * @param (Right|RightGroup)[] $rights - добавляемые права
    * @param RightGroup[] $parents - список родительских группа прав
    */
    final protected function addRights($rights, $parents = [])
    {
        $parent = end($parents);

        foreach ($rights as $item) {
            if ($item instanceof Right && !isset( $this->rights[$item->getAlias()])) {
                $item->setParents($parents);
                $this->rights[$item->getAlias()] = $item;
                if ($parent === false) {
                    $this->root_items[$item->getAlias()] = $item;
                } else {
                    $this->right_groups[$parent->getAlias()]->addChilds($item);
                }
            }

            if ($item instanceof RightGroup) {
                if (!isset($this->right_groups[$item->getAlias()])) {
                    $new_group = new RightGroup($item->getAlias(), $item->getTitle());
                    $this->right_groups[$new_group->getAlias()] = $new_group;
                    if ($parent === false) {
                        $this->root_items[$new_group->getAlias()] = $new_group;
                    } else {
                        $this->right_groups[$parent->getAlias()]->addChilds([$new_group]);
                    }
                }

                $this->addRights($item->getChilds(), array_merge($parents, [$item]));
            }
        }
    }

    /**
    * Возвращает существующие права
    *
    * @return Right[]
    */
    final public function getRights()
    {
        return $this->rights;
    }

    /**
    * Возвращает дерево существующих прав
    *
    * @return (Right|RightGroup)[]
    */
    final public function getRightsTree()
    {
        return $this->root_items;
    }

    /**
     * Возвращает список прав доступа для пользователя в формате:
     * ['alias_1' => true, 'alias_2' => false, ...]
     *
     * @param object $module Объект модуля, права которого нужно вернуть
     * @return array
     * @throws Exception
     */
    public static function getRightsData(object $module)
    {
        /**
         * @var AbstractModuleRights $right_object
         */
        $right_object = $module->getModuleConfig()->getModuleRightObject();
        if ($right_object == null) {
            return null;
        }
        $rights_list = $right_object->getRights();

        $rights = [];

        foreach ($rights_list as $right) {
            $alias = $right->getAlias();
            $rights[$alias] = Rights::hasRight($module, $alias);
        }

        return $rights;
    }

    /**
     * Рекурсивно возвращает список прав доступа для пользователя из древовидного объекта в формате:
     * ['alias_1' => true, 'alias_2' => false, ...]
     *
     * @param object $module Объект модуля, права которого нужно вернуть
     * @param (Right|RightGroup)[] $rights_tree Древовидный список объектов прав
     * @param array $rights Рекурсивно дополняющийся список прав доступа
     * @return array
     * @throws RSException
     */
    public static function getRightsDataTree(object $module, array $rights_tree, array $rights = [])
    {
        $new_childs = [];

        foreach ($rights_tree as $item) {
            if ($item instanceof RightGroup) {
                foreach ($item->getChilds() as $child) {
                    $new_childs[] = $child;
                }
            } else if ($item instanceof Right) {
                $alias = $item->getAlias();
                $rights[$alias] = Rights::hasRight($module, $alias);
            }
        }

        if ($new_childs) {
            $rights = self::getRightsDataTree($module, $new_childs, $rights);
        }

        return $rights;
    }

    /**
     * Возвращает список прав доступа, помещенных в группу (1ого или 2ого уровня вложенности)
     *
     * @param object $module Объект модуля, права которого нужно вернуть
     * @param string $group_alias alias группы (1ого или 2ого уровня вложенности)
     * @return array
     * @throws RSException
     */
    public static function getRightsDataGroupAlias($module, $group_alias = null) {
        /**
         * @var AbstractModuleRights $right_object
         */
        $right_object = $module->getModuleConfig()->getModuleRightObject();
        if ($right_object == null) {
            return null;
        }
        $rights_tree = $right_object->getRightsTree();
        $tree_list = null;
        if ($group_alias) {
            if ($rights_tree[$group_alias]) {
                $tree_list = $rights_tree[$group_alias]->getChilds();
            } else {
                foreach ($rights_tree as $tree_item) {
                    if ($tree_item->getChilds()[$group_alias]) {
                        $tree_list = $rights_tree[$group_alias]->getChilds();
                        break;
                    }
                }
            }
        }

        return self::getRightsDataTree($module, $tree_list);
    }

    /**
    * Проверяет наличие права
    *
    * @param string $alias идентификатор права
    * @return bool
    */
    final public function hasRight($alias)
    {
        return isset($this->rights[$alias]);
    }
    
    /**
    * Возвращает наименование права, или false если права не существует
    * 
    * @param mixed $alias
    * @return string|false
    */
    final public function getRightTitleWithPath($alias)
    {
        if ($this->hasRight($alias)) {
            return $this->rights[$alias]->getTitleWithPath();
        }
        return false;
    }

    /**
     * Исполняет инструкции автоматической проверки прав
     * в случае успеха возвращает false, иначе - текст ошибки
     *
     * @param string $type - тип объектов автоматической проверки
     * @param array $params - параметры для проверки
     * @return string|false
     */
    final public function checkErrorAutoCheckers($type, $params)
    {
        foreach ($this->auto_checkers[$type] as $checker) {
            if ($error = $checker->checkError($params)) {
                return $error;
            }
        }
        return false;
    }
}
