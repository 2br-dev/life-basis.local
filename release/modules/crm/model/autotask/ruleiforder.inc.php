<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use Catalog\Model\WareHouseApi;
use Crm\Model\Links\Type\LinkTypeOrder;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\PaymentApi;
use Shop\Model\UserStatusApi;

class RuleIfOrder extends AbstractIfRule
{
    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'order';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Заказ');
    }

    /**
     * Возвращает тип связи для объекта
     *
     * @return string
     */
    public function getLinkedTypeId()
    {
        return LinkTypeOrder::getId();
    }

    /**
     * Возвращает массив действий над объектом по типу
     *
     * @return array
     */
    public function getOperationsByType($type)
    {
        if ($type == 'insert') {
            return ['create'];
        }
        if ($type == 'update') {
            return ['change'];
        }
        return [];
    }

    /**
     * Возвращает действия, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getActions()
    {
        return parent::getActions() + [
            'create' => t('Создан'),
            'change' => t('Изменен'),
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
        $actions = [
            'status' => t('Статус'),
            'delivery' => t('Доставка'),
            'payment' => t('Тип оплаты'),
            'warehouse' => t('Склад'),
            'manager_user_id' => t('Менеджер заказа'),
            'is_payed' => t('Заказ оплачен?')
        ];

        if ($action == 'create') {
            $actions['creator_platform_id'] = t('Платформа, на которой был создан заказ');
        }
        if ($action == 'change') {
            $actions['cart_md5'] = t('Состав заказа изменился?');
        }

        return $actions;
    }

    /**
     * Модифицирует значение условия, если требуется
     *
     * @param $item
     * @return mixed $item
     */
    protected function modifyParamsItem($item)
    {
        if ($item['key'] == 'is_payed') {
            $item['value'] = 'on' ? 1 : 0;
        }
        if ($item['key'] == 'cart_md5' && $item['value'] == 'on') {
            $this->entity[$item['key']] = $item['value'] = $this->entity->getProductsHash();
        }
        return $item;
    }


    /**
     * Возвращает тип поля для шаблона
     *
     * @return string
     */
    public function getNodeType($key)
    {
        $node_type = 'select';
        if ($key == 'is_payed' || $key == 'cart_md5') {
            $node_type = 'checkbox';
        }
        return $node_type;
    }

    /**
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [
            'order_num' => t('Номер заказа'),
            'total_cost' => t('Сумма заказа'),
            'client_name' => t('ФИО клиента заказа'),
            'address' => t('Адрес доставки'),
            'payment' => t('Название способа оплаты'),
            'delivery' => t('Название способа доставки')
        ];
    }

    /**
     * Возвращает значения переменных, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceValues($entity)
    {
        $for_replace = [
            '{order_num}' => $entity['order_num'],
            '{total_cost}' =>  \RS\Helper\CustomView::cost($entity['totalcost']),
            '{client_name}' => $entity->getUser()->getFio(),
            '{address}' => $entity->getAddress()->getLineView(),
            '{payment}' => $entity->getPayment()->title,
            '{delivery}' => $entity->getDelivery()->title
        ];

        return $for_replace;
    }

    public function replaceSpecialChars($data)
    {
        $modifiedData = [];

        foreach ($data as $key => $string) {
            $modifiedData[$key] = str_replace(['&nbsp;', '&quot;'], ['-', '"'], $string);
        }

        return $modifiedData;
    }

    /**
     * Возвращает статусы
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->replaceSpecialChars(UserStatusApi::staticSelectList());
    }

    /**
     * Возвращает доставки
     *
     * @return array
     */
    public function getDelivery()
    {
        return $this->replaceSpecialChars(DeliveryApi::staticSelectList());
    }

    /**
     * Возвращает склады
     *
     * @return array
     */
    public function getWarehouse()
    {
        return $this->replaceSpecialChars(WareHouseApi::staticSelectList());
    }

    /**
     * Возвращает типы оплаты
     *
     * @return array
     */
    public function getPayment()
    {
        return $this->replaceSpecialChars(PaymentApi::staticSelectList());
    }

    /**
     * Возвращает список пользователей-менеджеров заказов.
     *
     * @return array
     */
    public function getManagerUserId()
    {
        return $this->replaceSpecialChars(OrderApi::getUsersManagersName());
    }

    /**
     * Возвращает список возможных платформ для создания сайта
     *
     * @return array
     */
    public function getCreatorPlatformId()
    {
        return $this->replaceSpecialChars(OrderApi::getCreatorPlatformsListTitles());
    }
}