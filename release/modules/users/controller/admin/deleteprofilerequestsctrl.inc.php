<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin;

use RS\Controller\Admin\Crud;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use Users\Model\DeleteProfileRequestsApi;

/**
* Контроллер запросов на удаление профиля
*/
class DeleteProfileRequestsCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new DeleteProfileRequestsApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Запросы на удаление'));
        $helper->setTopToolbar(null);
        $helper->setBottomToolbar($this->buttons(['delete']));

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Usertpl('user_id', t('Пользователь'), '%shop%/admin/order_user_cell.tpl', [
                    'allowLinks' => true,
                ]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('id', '№'),
                        new Filter\Type\User('user_id', t('Пользователь')),
                    ]]),
                ],
            ]),
            'Caption' => t('Поиск'),
        ]));

        return $helper;
    }
}

