<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\Autotask\AbstractIfRule;
use Crm\Model\Autotask\AbstractThenRule;
use Crm\Model\Orm\AutoTask;
use RS\Event\Event;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

/**
 * API для работы с авто задачами
 */
class AutoTaskApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\AutoTask());
    }

    /**
     * Возвращает отсортированный список доступных объектов для условий
     *
     * @return AbstractIfRule[][]
     */
    public static function getAllIfRules($first = [])
    {
        $rules = AbstractIfRule::getAllIfRules();
        $result = [];
        foreach($rules as $rule) {
            $result[$rule->getId()] = $rule->getTitle();
        }

        return $first + $result;
    }

    /**
     * Возвращает отсортированный список доступных объектов для действий
     *
     * @return AbstractThenRule[][]
     */
    public static function getAllThenRules($first = [])
    {
        $rules = AbstractThenRule::getAllThenRules();
        $result = [];
        foreach($rules as $rule) {
            $result[$rule->getId()] = $rule->getTitle();
        }

        return $first + $result;
    }

    /**
     * Возвращает автозадачи по типу условия
     *
     * @param $rule_if_class
     * @param bool $cache
     * @return array|mixed
     */
    public static function getAutoTasks($event)
    {
        $if_types = self::getIfTypes($event);

        if (empty($if_types)) {
            return [];
        }

        return Request::make()
            ->from(new AutoTask())
            ->where(['enable' => 1])
            ->whereIn('if_type', $if_types)
            ->objects();
    }

    /**
     * Возвращает список типов условий (if_type), поддерживающих указанное событие.
     *
     * @param Event $event Событие, по которому подбираются условия
     * @return string[] Массив идентификаторов типов условий
     */
    public static function getIfTypes($event)
    {
        $eventName = $event->getEvent();
        $result = [];

        preg_match('/-([^\\-]+)$/', $eventName, $matches);
        $eventType = $matches[1] ?? '';

        if (!$eventType) {
            return $result;
        }

        foreach (self::getAllIfRules() as $id => $title) {
            $if_class = AbstractIfRule::getClassById($id);

            if ($if_class && in_array($eventType, $if_class::getSupportsEvent(), true)) {
                $result[] = $id;
            }
        }

        return $result;
    }

    /**
     * Выполняет проверку на необходимость создания автозадач
     *
     * @param array $params Параметры
     * @param Event|null $event Только для AbstractIfRule::MODE_EVENT
     * @param string $mode Тип запуска: AbstractIfRule::MODE_CRON или MODE_EVENT
     */
    public static function run($params, $event = null, $mode = AbstractIfRule::MODE_EVENT)
    {
        $classes = AbstractIfRule::getClassesByMode($mode);

        if ($mode === AbstractIfRule::MODE_EVENT) {
            if (!$event) {
                return;
            }
            $tasks = self::getAutoTasks($event);
        } else {
            $tasks = Request::make()
                ->from(new AutoTask())
                ->where([
                    'enable' => 1
                ])
                ->whereIn('if_type', $classes)
                ->objects();
        }

        if ($tasks) {
            foreach($tasks as $task) {
                $if_class = AbstractIfRule::getClassById($task['if_type']);
                if ($if_class && $autotask = $if_class::match($task, $if_class, $params)) {
                    AbstractThenRule::run($autotask);
                }
            }
        }
    }
}