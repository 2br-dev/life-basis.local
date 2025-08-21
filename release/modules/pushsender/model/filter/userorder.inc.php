<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Filter;

use RS\Html\Filter\Type\DateRange;
use RS\Orm\Request;
use Shop\Model\Orm\Order;

/**
 * Фильтрует Push-токены по наличию заказов пользователей
 * в указанном диапазоне дат
 */
class UserOrder extends DateRange
{

    /**
     * Модифицирует запрос на фильтрацию
     *
     * @param Request $q
     */
    public function modificateQuery(Request $q)
    {
        $value = $this->getValue();
        if (!empty($value['from']) || !empty($value['to'])) {
            $user_query = Request::make()
                ->select('user_id')
                ->from(new Order());

            if (!empty($value['from'])) {
                $user_query->where("dateof >= '#date_from'", [
                    'date_from' => $value['from']
                ]);
            }
            if (!empty($value['to'])) {
                $user_query->where("dateof <= '#date_to'", [
                    'date_to' => $value['to'].' 23:59:59'
                ]);
            }

            $users_id = $user_query->exec()->fetchSelected(null, 'user_id');
            if (!$users_id) {
                $users_id = [0];
            }

            $q->whereIn('A.user_id', $users_id);
        }

        return $q;
    }

    /**
     * Возвращает выражение для поиска по дате
     */
    protected function where_daterange()
    {
        return '';
    }
}