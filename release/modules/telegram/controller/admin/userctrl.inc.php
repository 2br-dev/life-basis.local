<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use Telegram\Model\Orm\TelegramUser;
use Telegram\Model\TelegramUserApi;

/**
 * Контроллер управления пользователями Telegram
 */
class UserCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new TelegramUserApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Пользователи Telegram'));
        $helper->setTopHelp(t('Здесь отображаются пользователи из Telegram, которые хотя бы один раз взаимодействовали с вашими телеграм ботами'));
        $helper->setTopToolbar(null);

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('external_id', ['showSelectAll' => true]),
                new TableType\Text('first_name', t('Имя'), ['Sortable' => SORTABLE_BOTH,
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@external_id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('username', t('Никнейм'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('status', t('Статус'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\StrYesno('is_bot', t('Это бот'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Userfunc('ban_type', t('Заблокирован'), function($value, $cell) {
                    if ($cell->getRow()->ban_type) {
                        if ($cell->getRow()->ban_type == TelegramUser::BAN_FOREVER) {
                            return t('Да, бессрочно');
                        }
                        elseif ($cell->getRow()->ban_type == TelegramUser::BAN_TEMPORARY) {
                            return t('До %0', [$cell->getRow()->ban_expire]);
                        }
                    }
                    return $value;
                }, ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('ban_reason', t('Причина блокировки'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('external_id', t('ID в Телеграм'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Actions('external_id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]]),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));

        //Параметры фильтра
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('external_id', 'ID', ['Attr' => ['size' => 4]]),
                        new Filter\Type\Text('first_name', t('Имя'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('username', t('Никнейм'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('status', t('Статус'), ['SearchType' => '%like%']),
                        new Filter\Type\Select('is_bot', t('Это бот?'), [
                            '' => t('Не важно'),
                            '1' => t('Да'),
                            '0' => t('Нет'),
                        ]),
                        new Filter\Type\Select('is_bot', t('Тип блокировки'), [
                            '' => t('Не важно'),
                            '0' => t('Нет'),
                            '1' => t('Бессрочно'),
                            '2' => t('Временно'),
                        ]),
                    ]
                    ])
                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        return $helper;
    }

    public function helperEdit()
    {
        $helper = parent::helperEdit();
        $helper->setTopTitle(t('Редактировать пользователя Telegram {username}'));
        return $helper;
    }

}