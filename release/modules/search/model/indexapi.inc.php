<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model;

use RS\Module\AbstractModel\EntityList;

/**
* Api - для работы с индексной таблицей. Позволяет добавлять и исключать объекты из поиска
*/
class IndexApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Index);
    }
    
    public static $i = 0;
    
    /**
    * Добавляет к поиску или обновляет запись для поиска
    * 
    * @param string $result_class Класс результата поиска
    * @param integer $entity_id ID объекта результата поиска
    * @param string $title Название объекта результата поиска
    * @param string $index_text Индексируемый текст
    * @param string $dateof Дата последнего изменения объекта
    */
    public static function updateSearch($result_class, $entity_id, $title, $index_text, $dateof = null)
    {
        $search_item = new Orm\Index();
        $search_item['result_class'] = is_object($result_class) ? get_class($result_class) : $result_class;
        $search_item['entity_id'] = $entity_id;
        $search_item['title'] = $title;
        $search_item['indextext'] = strip_tags($index_text);
        $search_item['dateof'] = ($dateof === null) ? date('c') : $dateof;
         
        SearchApi::currentEngine()->onUpdateSearch($search_item);
        $search_item->replace();
    }
    
    /**
    * Удаляет запись из поиска
    *
    * @param string $result_class Класс результата поиска
    * @param integer $entity_id ID объекта результата поиска
    * @return void
    */
    public static function removeFromSearch($result_class, $entity_id)
    {
        $search_item = new Orm\Index();
        $search_item['result_class'] = is_object($result_class) ? get_class($result_class) : $result_class;
        $search_item['entity_id'] = $entity_id;
        $search_item->delete();
    }
}


