<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Filter;

use RS\Html\Filter\Type\AbstractType;
use RS\Orm\Request;

/**
 * Добавляет фильтр по колонкам цен, включенных для отображения в таблице
 */
class CostFilter extends AbstractType
{
    public $tpl = '%catalog%/filter/cost_filter.tpl';
    public $cost_id;
    public $costs = [];

    function __construct($costs, $options = [])
    {
        foreach($costs as $cost) {
            $this->costs[$cost['id']] = $cost;
        }
        parent::__construct('cost', '', $options);
    }

    /**
     * Модифицирует запрос с учетом выбранных фильтров
     *
     * @param Request $q - объект выборки данных из базы
     * @return Request
     */
    function modificateQuery(Request $q)
    {
        foreach($this->getValue() as $cost_id => $fromto) {
            if ((float)$fromto['from'] != 0) {
                $q->where('`XC'.(int)$cost_id."`.`cost_val` >= '#from'", [
                    'from' => (float)$fromto['from']
                ]);
            }
            if ((float)$fromto['to'] != 0) {
                $q->where('`XC'.(int)$cost_id."`.`cost_val` <= '#to'", [
                    'to' => (float)$fromto['to']
                ]);
            }
        }
        return $q;
    }

    /**
     * Фильтрацию осуществляет метод modificateQuery
     *
     * @return string
     */
    function getWhere()
    {
        return '';
    }


    /**
     * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
     *
     * @param array $current_filter_values - значения установленных фильтров
     * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
     * @return array of array ['title' => string, 'value' => string, 'href_clean']
     */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];
        foreach($this->getValue() as $cost_id => $fromto) {
            $value_array = [];
            if ((float)$fromto['from'] != 0) {
                $value_array[] = t('от ').$fromto['from'];
            }
            if ((float)$fromto['to'] != 0) {
                $value_array[] = t('до ').$fromto['to'];
            }
            if ($value_array) {
                $without_this = $current_filter_values;
                unset($without_this[$this->getKey()][$cost_id]);

                $parts[] = [
                    'title' => t('Цена - ') . $this->costs[$cost_id]['title'],
                    'value' => implode(' ', $value_array),
                    'href_clean' => \RS\Http\Request::commonInstance()->replaceKey([$this->wrap_var => $without_this]) //Url, для очистки данной части фильтра
                ];
            }
        }

        return $parts;
    }


    /**
     * Возвращает значение фильтра
     *
     * @return array
     */
    function getValue()
    {
        return array_map(function($item) {
            //Нормализуем состав ключей фильтра
            return $item + [
                    'from' => null,
                    'to' => null
                ];
        }, (is_array($this->value) ? $this->value : []));
    }
}