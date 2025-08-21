<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

use RS\Helper\Tools;

/**
 * Фильтр с множественным выбором значений из справочника
 */
class MultipleSelect extends AbstractType
{
    public
        $tpl = 'system/admin/html_elements/filter/type/select.tpl';

    protected
        $search_type = 'in',
        $attr = [
            'multiple' => true,
            'size' => 5
        ],
        $list;

    function __construct($key, $title, $list, $options = [])
    {
        $this->list = $list;
        parent::__construct($key, $title, $options);
    }

    /**
     * Возвращает список возможных значений отображаемого списка
     *
     * @return array
     */
    function getList()
    {
        return $this->list;
    }

    /**
     * Возвращает текстовое значение выбранного элемента списка
     *
     * @return string
     */
    function getTextValue()
    {
        $parts = [];
        $values = $this->getValue() ?: [];
        foreach($values as $value) {
            //Удаляем пробелы у древовидных списков
            $parts[] = preg_replace('/^(&nbsp;)+/', '', $this->list[$value]);
        }

        return implode(', ', $parts);
    }

    /**
     * @return string
     */
    protected function where_in()
    {
        if ($values = $this->getValue()) {
            return "{$this->getSqlKey()} IN (".implode(',', Tools::arrayQuote($values)).")";
        }
        return '';
    }

    /**
     * Возвращает имя переменной формы
     *
     * @return string
     */
    function getName()
    {
        return parent::getName().'[]';
    }
}