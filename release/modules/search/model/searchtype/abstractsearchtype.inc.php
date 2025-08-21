<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model\SearchType;

use RS\Orm\Request;

/**
 * Базовый класс, описывающий обработку параметров перед поиском для конкретного типа объектов
 */
abstract class AbstractSearchType
{
    protected $search_engine;

    /**
     * @param $engine
     * @return void
     */
    function init($engine)
    {
        $this->search_engine = $engine;
    }

    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска
     *
     * @param Request $q объект запроса
     * @param string $alias_product псевдоним таблицы с товарами
     * @param string $alias псевдоним таблицы с поисковыми данными
     * @return mixed
     */
    abstract public function afterJoinQuery(Request $q, $alias_product = 'A', $alias = 'B');
}