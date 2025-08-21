<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use Crm\Model\Links\Type\LinkTypeReservation;

class RuleIfReservation extends AbstractIfRule
{
    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'reservation';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Предварительный заказ');
    }

    /**
     * Возвращает тип связи для объекта
     *
     * @return string
     */
    public function getLinkedTypeId()
    {
        return LinkTypeReservation::getId();
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
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [
            'id' => t('Номер заявки'),
            'product_barcode' => t('Артикул продукта'),
            'product_title' => t('Название товара'),
            'offer' => t('Название комплектации'),
            'amount' => t('Количество'),
            'phone' => t('Телефон клиента'),
            'email' => t('E-mail'),
            'status' => t('Статус')
        ];
    }

    /**
     * Возвращает значения переменных, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceValues($entity)
    {
        return [
            '{id}' => $entity['id'],
            '{product_barcode}' => $entity['product_barcode'],
            '{product_title}' => $entity['product_title'],
            '{offer}' => $entity['offer'],
            '{amount}' => $entity['amount'],
            '{phone}' => $entity['phone'],
            '{email}' => $entity['email'],
            '{status}' => $entity['__status']->textView()
        ];
    }
}