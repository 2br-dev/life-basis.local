<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Statistic\Controller\Admin\Block;

use Statistic\Model\Components\DatePeriodSelector;
use Statistic\Model\Components\DatePeriodSelectorForWaves;
use Statistic\Model\Utils;

/**
* Блок "Ключевые показатели" в виде волн
*/
class KeyIndicatorsByWaves extends KeyIndicators
{
    protected $id;
    public $waves;
    public $default_wave = 'profit';

    /**
     * Конструктор класса
     * @param array $param
     */
    function __construct(array $param = [])
    {
        parent::__construct($param);
        $this->waves = [ //Текущие волны для отображения
            [
                'id' => t('profit'),
                'label' => t('Доход'),
            ],
            [
                'id' => t('totalCost'),
                'label' => t('Выручка')
            ],
            [
                'id' => t('ordersCount'),
                'label' => t('Количество заказов')
            ],
            [
                'id' => t('avgCost'),
                'label' => t('Средний чек')
            ],
            [
                'id' => t('ordersRepeatCount'),
                'label' => t('Повторные покупки')
            ],
            [
                'id' => t('ordersLTV'),
                'label' => t('LTV')
            ]
        ];
    }

    /**
     * Возвращает данные дохода за определенный период в виде массива
     *
     * @param DatePeriodSelectorForWaves $dps - объект класса селектора
     * @return array
     */
    private function getProfit(DatePeriodSelectorForWaves $dps)
    {
        return Utils::getOrdersSummaryProfitWaveData($dps->date_from, $dps->date_to, $dps->date_group);
    }

    /**
     * Возвращает данные выручки за определенный период в виде массива
     *
     * @param DatePeriodSelectorForWaves $dps - объект класса селектора
     * @return array
     */
    private function getTotalCost(DatePeriodSelectorForWaves $dps)
    {
        return Utils::getOrdersSummaryCostWaveData($dps->date_from, $dps->date_to, $dps->date_group);
    }

    /**
     * Возвращает данные количества заказов за определенный период в виде массива
     *
     * @param DatePeriodSelectorForWaves $dps - объект класса селектора
     * @return array
     */
    private function getOrdersCount(DatePeriodSelectorForWaves $dps)
    {
        return Utils::getOrdersCountWaveData($dps->date_from, $dps->date_to, $dps->date_group);
    }

    /**
     * Возвращает данные количества повторных заказов за определенный период в виде массива
     *
     * @param DatePeriodSelectorForWaves $dps - объект класса селектора
     * @return array
     */
    private function getOrdersRepeatCount(DatePeriodSelectorForWaves $dps)
    {
        return Utils::getRepeatedOrdersCountWaveData($dps->date_from, $dps->date_to, $dps->date_group);
    }

    /**
     * Возвращает данные по показателю LTV за определенный период в виде массива
     *
     * @param DatePeriodSelectorForWaves $dps - объект класса селектора
     * @return array
     */
    private function getOrdersLTV(DatePeriodSelectorForWaves $dps)
    {
        return Utils::getOrderLTVAverageCostWaveData($dps->date_from, $dps->date_to, $dps->date_group);
    }

    /**
     * Возвращает данные среднего чека за определенный период в виде массива
     *
     * @param DatePeriodSelectorForWaves $dps - объект класса селектора
     * @return array
     */
    private function getAvgCost(DatePeriodSelectorForWaves $dps)
    {
        return Utils::getOrderAverageWaveData($dps->date_from, $dps->date_to, $dps->date_group);
    }

    /**
     * Показ блока с данными
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $period_selector = new DatePeriodSelectorForWaves($this, [
            'date_group' => 'day',
            'wave' => $this->default_wave,
        ]);

        $raw_data  = $this->getRawData($period_selector);
        $flot_data = $this->getFlotData($period_selector, $raw_data);

        $this->view->assign([
            'ctrl' => $this,
            'period_selector' => $period_selector,
            'raw_data'    => $raw_data,
            'block_id'    => $this->id,
            'json_values' => json_encode($flot_data, JSON_UNESCAPED_UNICODE),
        ]);
        
        return $this->result->setTemplate( 'blocks/key_indicators_waves.tpl' );
    }

    /**
     * Возвращает данные в массиве для формирования графика
     *
     * @param DatePeriodSelector $dps - объект класса селектора
     * @param array $rawData - массив данных для графика
     * @return array
     */
    function getFlotData(DatePeriodSelector $dps, $rawData)
    {
        $data = []; //Данные графиков

        foreach($rawData as $index_title => $one)
        {
            $method_name = 'get'.$one['id'];

            if (method_exists($this, $method_name)){
                $data_values = $this->$method_name($dps);
                $ticks  = array_keys($data_values);   //Подписи для оси x
                $values = array_values($data_values); //Значения синусоид

                if ($dps->date_group == 'monthname'){ //Подменим значения названия месяцев
                    array_walk($ticks, function(&$value, $key){
                        $value = Utils::prepareMonth($value);
                    });
                }

                $data[$one['id']] = [
                    'id'    => $one['id'],
                    'label' => $one['label'],
                    'y_unit'  => (in_array($one['id'], ['ordersCount', 'ordersRepeatCount'])) ? t('шт.') : \Catalog\Model\CurrencyApi::getBaseCurrency()->stitle,
                    'data'  => [
                        'ticks'  => $ticks,
                        'values' => $values
                    ],
                ];
            }
        }

        return $data;
    }
}

