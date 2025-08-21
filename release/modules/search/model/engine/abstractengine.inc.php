<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model\Engine;

use RS\Orm\Request as OrmRequest;
use Search\Model\SearchType\AbstractSearchType;

/**
* Абстрактный класс поискового сервиса
*/
abstract class AbstractEngine
{
    const
        ORDER_RELEVANT = 'relevant',
        ORDER_FIELD = 'field';
        
    protected $config;
    protected $order;
    protected $order_type = self::ORDER_RELEVANT;
    protected $errors = [];
    protected $query;
    protected $filters;
    protected $search_type;

    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule($this);
    }

    /**
     * Устанавливает класс, который сможет модифицировать результат
     *
     * @param AbstractSearchType $search_type Объект модификатора (типа поиска)
     * @return self
     */
    public function setSearchType(AbstractSearchType $search_type)
    {
        $this->search_type = $search_type;
        $this->search_type->init($this);
        return $this;
    }
    
    /**
    * Возвращает название поискового сервиса
    * 
    * @return string
    */
    abstract public function getTitle();


    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска.
     *
     * @param OrmRequest $q - объект запроса
     * @param mixed $alias_product - псевдоним для таблицы товаров
     * @param mixed $alias - псевдоним для индексной таблицы
     * @return OrmRequest
     */
    abstract public function joinQuery(OrmRequest $q, $alias_product = 'A', $alias = 'B');


    /**
    * Устанавливает сортировку по релевантности
    * 
    * @return self
    */    
    function setOrderByRelevant()
    {
        $this->order_type = self::ORDER_RELEVANT;
        return $this;
    }
    
    /**
    * Устанавливает сортировку по полю $field
    * 
    * @param string $field
    * @return self
    */
    function setOrderByField($field)
    {
        $this->order_type = self::ORDER_FIELD;
        $this->order = $field;
        return $this;
    }
    
    /**
    * Устанавливает поисковый запрос для поиска
    * 
    * @param string $query
    * @return self
    */
    function setQuery($query)
    {
        $this->query = trim($query);
        return $this;
    }

    /**
     * Возвращает текущую поисковую строку
     *
     * @return string
     */
    function getQuery()
    {
        return $this->query;
    }
    
    /**
    * Устанавливает дополнительные фильтры, которые будут применены к поисковому индексу
    * 
    * @param string $key
    * @param mixed $value
    * @return self
    */
    function setFilter($key, $value)
    {
        if ($value === null) unset($this->filters[$key]);
            else $this->filters[$key] = $value;
        return $this;
    }
    
        
    /**
    * Добавляет сведения об ошибке
    * 
    * @param string $errorText текст ошибки
    * @return self
    */
    function addError($errorText)
    {
        $this->errors[] = $errorText;
        return $this;
    }
    
    /**
    * Возвращает ошибки, произошедшие во время поиска
    * 
    * @return array
    */
    function getErrors()
    {
        return $this->errors;
    }    
    
    /**
    * Модифицирует индексную таблицу
    * 
    * @param mixed $search_item
    */
    function onUpdateSearch($search_item)
    {}
}
