<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

class RuleIfSchedule extends AbstractIfRule
{
    protected static string $mode = self::MODE_CRON;

    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'schedule';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('По расписанию');
    }

    /**
     * Возвращает массив действий над объектом по типу
     *
     * @return array
     */
    public function getOperationsByType($type)
    {
        return ['daily', 'weekly', 'monthly'];
    }

    /**
     * Возвращает действия, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getActions()
    {
        return parent::getActions() + [
            'daily' => t('Ежедневно'),
            'weekly' => t('По дням недели'),
            'monthly' => t('По дням месяца'),
        ];
    }

    /**
     * Воздращает доступные для условия действия
     *
     * @return array
     */
    public function getAvailableActions()
    {
        return ['create'];
    }

    /**
     * Возвращает дополнительные параметры, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getParams($action = null)
    {
        switch ($action) {
            case 'daily':
                return ['time' => t('Время')];
            case 'weekly':
                return ['days' => t('Дни недели'), 'time' => t('Время')];
            case 'monthly':
                return ['month_days' => t('Дни месяца'), 'time' => t('Время')];
            default:
                return ['days' => t('Дни недели'), 'month_days' => t('Дни месяца'), 'time' => t('Время')];
        }
    }

    /**
     * Возвращает тип поля для шаблона
     *
     * @return string
     */
    public function getNodeType($key)
    {
        if ($key == 'time') return 'time';
        if ($key == 'days' || $key == 'month_days') return 'days';
        return null;
    }

    /**
     * Возвращает список дней недели
     *
     * @return array
     */
    public function getDays()
    {
        return [
            1 => t('Пн'),
            2 => t('Вт'),
            3 => t('Ср'),
            4 => t('Чт'),
            5 => t('Пт'),
            6 => t('Сб'),
            7 => t('Вс'),
        ];
    }

    /**
     * Возвращает список дней месяца
     *
     * @return array
     */
    public function getMonthdays()
    {
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = (string)$i;
        }
        return $days;
    }

    /**
     * Проверяет наступило ли сейчас нужное время, чтобы выполнить задачу по расписанию.
     *
     * @param array $params
     * @return bool
     */
    protected function isTaskDue(array $params): bool
    {
        $now = new \DateTime();
        $currentDayOfWeek = $now->format('N');
        $currentDayOfMonth = $now->format('j');
        $currentTime = $now->format('H:i');

        $targetTime = $params['time']['value'] ?? '00:00';

        // Ежедневная задача
        if (isset($params['time']) && count($params) === 1) {
            return $currentTime === $targetTime;
        }

        // По дням недели
        if (isset($params['days'])) {
            $days = $params['days']['value'];
            if (in_array($currentDayOfWeek, $days)) {
                return $currentTime === $targetTime;
            }
        }

        // По дням месяца
        if (isset($params['month_days'])) {
            $days = $params['month_days']['value'];
            if (in_array($currentDayOfMonth, $days)) {
                return $currentTime === $targetTime;
            }
        }

        return false;
    }

    /**
     * Проверяет, пора ли выполнить автозадачу по текущим параметрам (день/время)
     *
     * @return bool
     */
    public function compareParams()
    {
        $params = [];

        foreach ($this->rule['if_params_arr'] as $item) {
            $item = $this->modifyParamsItem($item);
            $params[$item['key']] = [
                'key' => $item['key'],
                'value' => $item['value']
            ];
        }

        return $this->isTaskDue($params);
    }
}