<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use Crm\Model\Links\Type\LinkTypeOneClickItem;

class RuleIfOneClick extends AbstractIfRule
{
    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'oneclickitem';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Покупка в 1 клик');
    }

    /**
     * Возвращает тип связи для объекта
     *
     * @return string
     */
    public function getLinkedTypeId()
    {
        return LinkTypeOneClickItem::getId();
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
            'create' => t('Создана'),
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
            'title' => t('Наименование заявки'),
            'client_name' => t('ФИО клиента'),
            'client_phone' => t('Телефон клиента'),
            'status' => t('Статус заявки'),
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
            '{title}' => $entity['title'],
            '{client_name}' => $entity->getUser()->getFio(),
            '{client_phone}' => $entity['user_phone'],
            '{status}' => $entity['__status']->textView(),
        ];

        return $for_replace;
    }
}