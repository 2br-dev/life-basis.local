<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\App;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use Shop\Model\Orm\Order;

/**
 * Интерфейс подсказывает, что Приложение может создавать заказы
 */
interface InterfaceOrderCreation
{
    /**
     * Возвращает строковый идентификатор создателя заказа, при создании заказа через API
     *
     * @return string
     */
    public function getCreatorPlatformId();

    /**
     * Возвращает дополнительную информацию
     *
     * @param AbstractMethod $method Объект метода API
     * @param Order $order Объект заказа
     */
    public function addOrderExtraData(AbstractMethod $method, Order $order);
}