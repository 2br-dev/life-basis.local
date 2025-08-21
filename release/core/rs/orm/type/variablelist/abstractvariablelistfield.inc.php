<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type\VariableList;

abstract class AbstractVariableListField
{
    protected $name;
    protected $column_title;
    protected $array_wrap;
    private $attributes = [];

    /**
     * AbstractVariableListField constructor.
     *
     * @param string $name - имя элемента
     * @param string $column_title - название колонки
     */
    public function __construct($name, $column_title)
    {
        $this->name = $name;
        $this->column_title = $column_title;
    }

    /**
     * Возвращает имя поля
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Возвращает название колонки
     *
     * @return string
     */
    public function getColumnTitle()
    {
        return $this->column_title;
    }

    /**
     * Устанавливает имя массива, в который будут обернуты name форм
     *
     * @param string $name
     * @return self
     */
    public function setArrayWrap($name)
    {
        $this->array_wrap = $name;
        return $this;
    }

    /**
     * Возвращает имя массива, в который будут обернуты name форм
     *
     * @param string $name
     */
    public function getArrayWrap()
    {
        return $this->array_wrap;
    }

    /**
     * Оборачивает $field_name массивом, если это установлено
     *
     * @param string $field_name имя поля
     * @return string
     */
    protected function wrapField($field_name)
    {
        if ($this->getArrayWrap()) {
            $field_name = $this->getArrayWrap()."[{$field_name}]";
        }

        return $field_name;
    }

    /**
     * Добавляет произвольные атрибуты для элемента формы
     *
     * @param array $key_value
     * @return self
     */
    public function setAttributes($key_value)
    {
        $this->attributes = $key_value;
        return $this;
    }

    /**
     * Возвращает произвольные атрибуты для элемента формы
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Возвращает произвольные атрибуты в строковом виде
     *
     * @return string
     */
    public function getAttributesInline()
    {
        $result = '';
        foreach($this->getAttributes() as $key => $value) {
            $result .= " {$key}=\"{$value}\"";
        }

        return $result;
    }

    /**
     * Возвращает html элемента
     *
     * @param string $field_name - имя ORM поля
     * @param string $row_index - индекс строки в таблице
     * @param mixed $value - значение
     * @return string
     */
    abstract public function getElementHtml($field_name, $row_index = '%index%', $value = null);
}
