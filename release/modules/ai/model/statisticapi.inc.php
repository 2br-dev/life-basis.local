<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use Ai\Model\Orm\Statistic;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

/**
 * API для работы с объектами статистики
 */
class StatisticApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Statistic());
    }

    /**
     * Возвращает суммарные значения использованных токенов для всей выборки
     *
     * @return array
     */
    public function getStatisticTokens()
    {
        $api = new self();
        $saved_request = $api->getSavedRequest('Ai\Controller\Admin\StatisticCtrl_list');

        if (!$saved_request) {
            $saved_request = clone $api->queryObj();
        }

        $saved_request->select = '';
        $saved_request->offset = null;
        $saved_request->limit = null;

        return $saved_request
            ->select('COUNT(*) as total_requests,
                                SUM(A.input_text_tokens) as input_tokens_sum, 
                                SUM(A.completion_tokens) as completion_tokens_sum, 
                                SUM(A.total_tokens) as total_tokens_sum')
            ->exec()
            ->fetchRow();
    }

    /**
     * Возвращает данные для диаграммы за последний год
     *
     * @param int|null $user_id Если задано, то будет фильтровать значения только для указанного user_id
     * @return array
     */
    public function statisticLastYear($user_id = null)
    {
        $date_from = date('Y-m-d H:i:s', strtotime('-1 year'));

        $query = Request::make()
            ->select("DATE_FORMAT(date_of_create,'%Y-%m') as yearmonth,
                                COUNT(*) as total_requests,
                                SUM(input_text_tokens) as input_tokens_sum, 
                                SUM(completion_tokens) as completion_tokens_sum, 
                                SUM(total_tokens) as total_tokens_sum")
            ->from(new Statistic())
            ->where("date_of_create >= '#date_from'", [
                'date_from' => $date_from
            ])
            ->groupby("DATE_FORMAT(date_of_create,'%Y-%m')");

        if ($user_id) {
            $query->where([
                'user_id' => $user_id
            ]);
        }

        $charts = [
            'total_requests' => t('Всего запросов'),
            'input_tokens_sum' => t('Токены в запросе'),
            'completion_tokens_sum' => t('Токены в ответе'),
            'total_tokens_sum' => t('Всего токенов'),
        ];

        $months = $this->getPreviousMonthsList();
        $rows = $query->exec()->fetchSelected('yearmonth');

        $chart_data = [];
        if ($rows) {
            foreach ($months as $year_month => $month_title) {
                $n = 0;
                foreach ($charts as $chart_key => $chart_title) {
                    $chart_data[$chart_key]['label'] = $chart_title;
                    $chart_data[$chart_key]['data'][] = [
                        $month_title,
                        (int)($rows[$year_month][$chart_key] ?? 0),
                    ];
                    $chart_data[$chart_key]['color'] = $n;
                    $n++;
                }
            }
        }

        return $chart_data;
    }

    /**
     * Возвращает последние $count названий месяцев
     *
     * @param int $count
     * @return array
     */
    function getPreviousMonthsList(int $count = 12)
    {
        $months = [];
        $currentDate = new \DateTime();

        for ($i = 0; $i < $count; $i++) {
            $date = clone $currentDate;
            $date->modify("-$i months");

            $year = $date->format('Y');
            $month = $date->format('m');

            $months[$year.'-'.$month] = strtotime("$year-$month-01") * 1000;
        }
        return array_reverse($months, true);
    }

    /**
     * Возвращает последние $count названий дней
     *
     * @param int $count
     * @return array
     */
    function getPreviousDaysList(int $count = 30)
    {
        $days = [];
        $currentDate = new \DateTime();

        for ($i = 0; $i < $count; $i++) {
            $date = clone $currentDate;
            $date->modify("-$i days");

            $date_string = $date->format('Y-m-d');
            $days[$date_string] = strtotime($date_string) * 1000;
        }

        return array_reverse($days, true);
    }

    /**
     * Возвращает данные для диаграммы за последний месяц
     *
     * @return array
     */
    public function statisticLastMonth($user_id = null)
    {
        $date_from = date('Y-m-d H:i:s', strtotime('-1 month'));

        $query = Request::make()
            ->select("DATE(date_of_create) as day,
                                COUNT(*) as total_requests,
                                SUM(input_text_tokens) as input_tokens_sum, 
                                SUM(completion_tokens) as completion_tokens_sum, 
                                SUM(total_tokens) as total_tokens_sum")
            ->from(new Statistic())
            ->where("date_of_create >= '#date_from'", [
                'date_from' => $date_from
            ])
            ->groupby("DATE(date_of_create)");

        if ($user_id) {
            $query->where([
                'user_id' => $user_id
            ]);
        }

        $charts = [
            'total_requests' => t('Всего запросов'),
            'input_tokens_sum' => t('Токены в запросе'),
            'completion_tokens_sum' => t('Токены в ответе'),
            'total_tokens_sum' => t('Всего токенов'),
        ];

        $days = $this->getPreviousDaysList();
        $rows = $query->exec()->fetchSelected('day');

        $chart_data = [];
        if ($rows) {
            foreach ($days as $date => $day_title) {
                $n = 0;
                foreach ($charts as $chart_key => $chart_title) {
                    $chart_data[$chart_key]['label'] = $chart_title;
                    $chart_data[$chart_key]['data'][] = [
                        $day_title,
                        (int)($rows[$date][$chart_key] ?? 0),
                    ];
                    $chart_data[$chart_key]['color'] = $n;
                    $n++;
                }
            }
        }

        return $chart_data;
    }
}