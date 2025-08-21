<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Crud;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use Shop\Model\BonusCardsApi;

/**
* Контроллер бонусных карт пользователей
*/
class BonusCardsCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new BonusCardsApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Сохранённые бонусные карты'));
        $helper->setTopHelp(t('Здесь отображаются все сохраненные пользователями бонусные карты.'));
        $helper->setTopToolbar(null);
        $helper->setBottomToolbar($this->buttons(['delete']));

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Usertpl('user_id', t('Пользователь'), '%shop%/admin/order_user_cell.tpl', [
                    'allowLinks' => true,
                ]),
                new TableType\Text('number', t('Номер бонусной карты'), ['Sortable' => SORTABLE_BOTH]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('id', '№'),
                        new Filter\Type\User('user_id', t('Пользователь')),
                        new Filter\Type\Text('number', t('Номер бонусной карты'), ['searchType' => '%like%']),
                    ]]),
                ],
            ]),
            'Caption' => t('Поиск по сохраненным бонусным картам'),
        ]));

        return $helper;
    }
}

