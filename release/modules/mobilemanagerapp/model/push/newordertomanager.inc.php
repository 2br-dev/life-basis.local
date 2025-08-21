<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;

/**
 * Push уведомление менеджеру о новом заказе
 */
class NewOrderToManager extends NewOrderToAdmin
{
    /*
    * Возвращает описание уведомления для внутренних нужд системы и
    * отображения в списках админ. панели
    *
    * @return string
    */
    public function getTitle()
    {
        return t('Новый заказ для менеджера');
    }

    /**
     * Возвращает одного или нескольких получателей
     *
     * @return array
     */
    public function getRecipientUserIds()
    {
        return $this->order->manager_user_id;
    }
}
