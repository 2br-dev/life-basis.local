<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin;

use RS\Controller\Admin\Crud;
use Users\Model\VerificationSessionApi;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;

/**
 * Контроллер, позволяет просматривать верификационные сессии в административной панели
 */
class VerificationCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new VerificationSessionApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Верификационные сессии'));
        $helper->setTopHelp(t('В этом разделе отображаются верификационные сессии. Верификационная сессия создается при возникновении подтверждения определенных действий через код, отправленный через провайдер (SMS, Email, и т.д.). Сессии автоматически удаляются после истечения их срока жизни, поэтому здесь вы можете видеть только недавно открытые сессии.'));
        $helper->setTopToolbar();

        $helper->setTable(new Table\Element([
            'Columns' => [
                    new TableType\Checkbox('uniq', ['showSelectAll' => true]),
                    new TableType\Text('uniq', t('Ключ сессии'), [
                        'Sortable' => SORTABLE_BOTH,
                        'CurrentSort' => SORTABLE_ASC,
                        'href' => $this->router->getAdminPattern('edit', [':id' => '@uniq']),
                        'linkAttr' => [
                            'class' => 'crud-edit'
                        ]
                    ]),
                    new TableType\Text('ip', 'IP-адрес', ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('user_session_id', t('ID сессии клиента'), ['Sortable' => SORTABLE_BOTH, 'Hidden' => true]),
                    new TableType\User('creator_user_id', t('Создатель'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('verification_provider', t('Провайдер'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('phone', t('Телефон'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('email', t('Email'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Datetime('code_expire', t('Истекает'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('send_counter', t('Счетчик отправки'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Datetime('send_last_time', t('Последняя отправка кода'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('try_counter', t('Число ввода кода'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Datetime('try_last_time', t('Последний ввод кода'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('action', t('Действие'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Datetime('last_initialized', t('Последняя инициализация'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\StrYesno('is_resolved', t('Код введен успешно'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Datetime('resolved_time', t('Дата успешного ввода'), ['Sortable' => SORTABLE_BOTH]),

                    new TableType\Actions('id', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '@uniq']), null, [
                                'attr' => [
                                    '@data-id' => '@uniq'
                                ]]),
                        ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ]
            ]));

        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['Items' => [
                        new Filter\Type\Text('id','№', ['attr' => ['class' => 'w100']]),
                        new Filter\Type\Text('uniq', t('Название'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('ip', t('IP-адрес'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('user_session_id', t('ID сессии пользователя'), ['SearchType' => '%like%']),
                        new Filter\Type\User('creator_user_id', t('Создатель')),
                        new Filter\Type\Text('verification_provider', t('Провайдер'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('phone', t('Телефон'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('email', t('Email'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('action', t('Действие'), ['SearchType' => '%like%']),
                        new Filter\Type\Select('is_resolved', t('Код введен успешно'), [
                            '' => t('Не важно'),
                            '1' => t('Да'),
                            '0' => t('Нет')
                        ]),
                    ]
                    ])
                ]
            ]),
            'Caption' => t('Поиск по сессиям'),
        ]));

        return $helper;
    }
}