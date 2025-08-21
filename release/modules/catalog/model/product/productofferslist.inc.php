<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Product;

use Catalog\Model\Orm\Offer;

/**
 * Класс объектов - список комплектаций товара
 */
class ProductOffersList implements \Iterator, \ArrayAccess, \Countable
{
    /** @var Offer[] */
    protected $list = [];

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * Возвращает количество элементов в списке
     *
     * @return int
     */
    public function count():int
    {
        return count($this->list);
    }

    /**
     * Возвращает true, если элемент с ключем offset в списке существует
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset):bool
    {
        return (isset($this->list[$offset]) || !$offset) && $this->list;
    }

    /**
     * Возвращает значение списка по ключу $offset
     *
     * @param string $offset
     * @return Offer|mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $offer_id = ($offset == 0) ? $this->getFirstKey() : $offset;
        return $this->list[$offer_id] ?? null;
    }

    /**
     * Устанавливает значение $value для ключа $offset
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value):void
    {
        $offer_id = ($offset == 0 && $this->list) ? $this->getFirstKey() : $offset;
        $this->list[$offer_id] = $value;
    }

    /**
     * Удаляет значение списка по ключу $offset
     * @param string $offset
     */
    public function offsetUnset($offset):void
    {
        if ($this->list) {
            $offer_id = ($offset == 0) ? $this->getFirstKey() : $offset;
            unset($this->list[$offer_id]);
        }
    }

    /**
     * Возвращает текущий элемент списка
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->list);
    }

    /**
     * Перемещает к следующему элементу списка
     */
    public function next():void
    {
        next($this->list);
    }

    /**
     * Возвращает текущий ключ списка
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->list);
    }

    /**
     * Возвращает true, если элемент с текущим ключем существует
     *
     * @return bool
     */
    public function valid():bool
    {
        return key($this->list) !== null;
    }

    /**
     * Переводит указатель в начало списка
     */
    public function rewind():void
    {
        reset($this->list);
    }
    /**
     * Возвращает ключ первого элемента в списке комплектаций
     *
     * @return int
     */
    protected function getFirstKey()
    {
        $list = $this->list;
        reset($list);
        return key($list);
    }

    /**
     * Сортирует массив с комплектациями с помощью произвольной функции
     *
     * @param callback $callback
     * @return bool
     */
    public function usort($callback)
    {
        return usort($this->list, $callback);
    }
}
