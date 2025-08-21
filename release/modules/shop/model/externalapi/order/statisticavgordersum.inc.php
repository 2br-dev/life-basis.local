<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use RS\Helper\CustomView;
use RS\Orm\Request;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\UserStatus;
use Shop\Model\UserStatusApi;

/**
* Возвращает средний чек
*/
class StatisticAvgOrderSum extends AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1;
        
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка данных')
        ];
    }

    /**
     * Возвращает true, если дата в формате YYYY-MM-DD корректная
     *
     * @param string $date
     * @return bool
     */
    protected function isValidDate($date)
    {
        list($year, $month, $day) = explode('-', $date);
        return checkdate($month, $day, $year);
    }
    
    /**
    * Возвращает средний чек пользователя
    * ---
    * В отчет попадают только завершенные заказы
    * 
    * @param string $token Авторизационный токен
    * @param string $period Отчетный период. Может быть month, year, all, custom
    * @param string $date_from Дата начала произвольного периода (обязательно, если period=custom)
    * @param string $date_to Дата окончания произвольного периода (обязательно, если period=custom)
    *
    * @example GET /api/methods/order.statisticAvgOrderSum?token=311211047ab5474dd67ef88345313a6e479bf616&period=custom&date_from=2022-01-01&date_to=2024-12-01
    *
    * <pre>
    *    {
            "response": {
                "date_from": "2022-01-01",
                "date_to": "2024-12-01",
                "currency": "₽",
                "orders_sum": 312201.8,
                "orders_count": 12,
                "average": 26016.82,
                "average_formatted": "26 016.82 ₽",
                "order_sum_formatted": "312 201.80 ₽"
            }
        }
    * </pre>
    *
    * @return array Возвращает массив с данными о количестве заказов, сумме и среднем чеке
    */
    public function process($token, $period, $date_from = null, $date_to = null)
    {
        $periods = ['month', 'year', 'all', 'custom'];
        if (!in_array($period, $periods)) {
            throw new ApiException(t('Неверное значение параметра period, допустимы значения: %0', [implode(', ', $periods)]));
        }

        if ($period == 'custom') {
            if (!$date_from || !$this->isValidDate($date_from)) {
                throw new ApiException(t('Некорректное значение начальной даты периода'));
            }
            if (!$date_to || !$this->isValidDate($date_to)) {
                throw new ApiException(t('Некорректное значение конечной даты периода'));
            }
        }
        
        switch($period) {
            case 'month': 
                $date_from = date('Y-m-d', strtotime('-1 month'));
                $date_to = date('Y-m-d');
                break;
            case 'year':
                $date_from = date('Y-m-d', strtotime('-1 year'));
                $date_to = date('Y-m-d');
                break;
            case 'all':
                $date_from = null;
                $date_to = null;
                break;            
        }
        
        $req = Request::make();
        $req->from(new Order);
        $req->where([
            'site_id' => SiteManager::getSiteId()
        ]);
        
        if ($date_from) {
            $req->where("dateof >= '#date_from' AND dateof <= '#date_to'", [
                'date_from' => $date_from,
                'date_to' => $date_to.' 23:59:59'
            ]);
        }
        
        $status_ids = UserStatusApi::getStatusesIdByType(UserStatus::STATUS_SUCCESS);
        if (!$status_ids) {
            throw new ApiException(t('Не найдены success статусы'));
        }
        $req->whereIn('status',  $status_ids);

        $req->select('AVG(totalcost) as average, COUNT(id) as orders_count, SUM(totalcost) as orders_sum');
        $row = $req->exec()->fetchRow();
        $average = $row['average'] ? round($row['average'], 2) : 0;
        $count = (int)($row['orders_count'] ?? 0);
        $sum = (float)($row['orders_sum'] ?? 0);
        $currency = CurrencyApi::getBaseCurrency()->stitle;

        $result = [
            'response' => [
                'date_from' => $date_from,
                'date_to' => $date_to,
                'currency' => $currency,
                'orders_sum' => $sum,
                'orders_count' => $count,
                'average' => $average,
                'average_formatted' => CustomView::cost($average, $currency),
                'order_sum_formatted' => CustomView::cost($sum, $currency),
                'orders_count_formatted' => number_format($count, 0, '.', ' ')
            ]
        ];
        
        return $result;
    }
}
