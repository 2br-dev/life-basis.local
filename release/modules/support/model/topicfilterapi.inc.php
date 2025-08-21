<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

use RS\Application\Auth;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use Support\Model\Orm\Topic;
use Support\Model\Orm\TopicFilter;

/**
 * Класс для работы с сохраненными пользовательскими фильтрами
 */
class TopicFilterApi extends EntityList
{
    public $uniq;

    function __construct()
    {
        parent::__construct(new TopicFilter(), [
            'multisite' => true,
            'defaultOrder' => 'sortn',
            'nameField' => 'title',
            'sortField' => 'sortn'
        ]);
    }

    /**
     * Возвращает выборку сохраненных фильтров для текущего пользователя
     *
     * @param int $user_id - id пользователя, если не указан то берётся у текущего пользователя
     * @return TaskFilter[]
     */
    function getCategoryList($user_id = null)
    {
        if ($user_id === null) {
            $user = Auth::getCurrentUser();
            $user_id = $user['id'];
        }

        $this->setFilter('user_id', $user_id);
        /** @var TaskFilter[] $list */
        $list = $this->getList();
        return $list;
    }

    /**
     * Возвращает набор фильтров, сохраненных в пресете $id
     *
     * @param integer $id
     * @param integer|null $user_id
     * @return array
     */
    function getFiltersByPresetId($id, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::getCurrentUser()->id;
        }

        $filter = Request::make()
            ->from($this->getElement())
            ->where([
                'user_id' => $user_id,
                'id' => $id
            ])->object();


        if ($filter) {
            return $filter->unserializeFilters();
        }

        return [];
    }

    /**
     * Перемещает элемент from на место элемента to. Если flag = 'up', то до элемента to, иначе после
     *
     * @param int $from - id элемента, который переносится
     * @param int $to - id ближайшего элемента, возле которого должен располагаться элемент
     * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
     * @param \RS\Orm\Request $extra_expr - объект с установленными уточняющими условиями, для выборки объектов сортировки
     * @return bool
     */
    function moveElement($from, $to, $flag, \RS\Orm\Request $extra_expr = null)
    {
        if (!$extra_expr) {
            $user_id = Auth::getCurrentUser()->id;
            $extra_expr = \RS\Orm\Request::make()->where([
                'user_id' => $user_id
            ]);
        }

        return parent::moveElement($from, $to, $flag, $extra_expr);
    }
}