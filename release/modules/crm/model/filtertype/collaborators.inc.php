<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilterType;

use Crm\Model\Orm\UserLink;
use RS\Html\Filter\Type\User;

/**
 * Фильтр по исполнителю и соисполнителю
 */
class Collaborators extends User
{
    private $source_type;

    public function __construct($key, $source_type, $title, $options = [])
    {
        $this->source_type = $source_type;
        parent::__construct($key, $title, $options);
    }


    /**
     * Модифицирует запрос для реализации фильтра. Можно использовать, когда для
     * фильтрации нужно подключать дополнительные таблицы
     *
     * @param \RS\Orm\Request $q
     * @return false|mixed|\RS\Orm\Request
     */
    function modificateQuery(\RS\Orm\Request $q)
    {
        if ($this->getValue()) {
            $q->leftjoin(new UserLink(), "UL3.source_id = A.id AND UL3.source_type = '" . $this->source_type .
                "' AND UL3.user_role = '" . UserLink::USER_ROLE_COLLABORATOR . "'", 'UL3')
            ->groupby('A.id');
        }

        return parent::modificateQuery($q);
    }

    /**
     * Сравнивает используя равенство или если включен showtypes, использует > (больше) или < (меньше)
     * @param string $compare метод сравнения
     */
    public function where_eq($compare = '=')
    {
        $user_id = (int)$this->getValue();
        if ($user_id) {
            return "(A.{$this->key} = '{$user_id}' OR UL3.user_id = '{$user_id}')";
        }
        return '';
    }
}