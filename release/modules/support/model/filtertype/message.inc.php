<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Filtertype;

use RS\Html\Filter\Type\AbstractType;
use Support\Model\Orm\Support;

/**
 * Фильтр по сообщениям в тикете
 */
class Message extends AbstractType
{
    public
        $tpl = 'system/admin/html_elements/filter/type/string.tpl';

    protected
        $search_type = 'eq';

    /**
     * Модифицирует запрос
     *
     * @param \RS\Orm\Request $q
     * @return \RS\Orm\Request
     */
    function modificateQuery(\RS\Orm\Request $q)
    {
        //Если указано значение и таблица ещё не присоединена
        if (!empty($this->value) && !$q->issetTable(new Support())) {
            $q->select = 'A.*';
            $q->join(new Support(), 'A.id = S.topic_id', 'S');
            $q->groupby('A.id');
        }
        
        return $q;
    }
}