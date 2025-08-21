<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Exception;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Html\Toolbar\Button\Button;
use Telegram\Model\MessageProcessor;
use Telegram\Model\Orm\Profile;
use Telegram\Model\ProfileApi;

/**
 * Контроллер Управление скидочными купонами
 */
class ProfileCtrl extends Crud
{
    protected
        $api;

    function __construct()
    {
        parent::__construct(new ProfileApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В этом разделе можно создать профиль для Telegram бота, который будет транслировать события из Telegram в ReadyScript и наоборот'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить профиль')]));
        $helper->getTopToolbar()
            ->addItem(
                new Button($this->router->getAdminUrl(false, [], 'telegram-userctrl'), t('Пользователи из telegram')));
        $helper->setTopTitle(t('Профили для Telegram ботов'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('bot_name', t('Идентификатор'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\StrYesno('is_webhook_enabled', t('Веб-хуки включены'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\StrYesno('is_default', t('По-умолчанию')),
                new TableType\Yesno('is_enable', t('Включен'), [
                    'toggleUrl' => $this->router->getAdminPattern('ajaxToggleEnable', [':id' => '@id'])
                ]),
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Загрузить новые события'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('handleMessages', [':id' => '~field~']),
                            ]
                        ],
                        [
                            'title' => t('Установить Web-хуки'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('enableWebhook', [':id' => '~field~']),
                            ]
                        ],
                        [
                            'title' => t('Отключить Web-хуки'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('disableWebhook', [':id' => '~field~']),
                            ]
                        ],
                    ]),
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
                        new Filter\Type\Text('id', '№', ['Attr' => ['size' => 4]]),
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                        new Filter\Type\Select('is_enable', t('Включен'), [
                            '' => t('Не важно'),
                            '1' => t('Да'),
                            '0' => t('Нет'),
                        ]),
                    ]
                    ])
                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        return $helper;
    }

    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKey === null) {
            $profile = $this->api->getElement();
            $profile['is_enable'] = 1;
            $profile['process_message_in_support'] = 1;
            $profile['support_notice_ticket_create'] = 1;
            $profile['support_notice_ticket_close'] = 1;
        }

        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    /**
     * Включает/выключает флаг "включен"
     */
    function actionAjaxToggleEnable()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);

        $profile = $this->api->getOneItem($id);
        if ($profile) {
            $profile['is_enable'] = !(int)$profile['is_enable'];
            $profile->update();
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Включает получение хуков
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionEnableWebhook()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $profile = new Profile($id);
        if ($profile->setWebhooks()) {
            return $this->result->setSuccess(true)->addMessage(t('Веб-хук успешно установлен'));
        } else {
            return $this->result->setSuccess(false)
                ->addEMessage($profile->getErrorsStr());
        }
    }

    /**
     * Отключает получение хуков
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDisableWebhook()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $profile = new Profile($id);
        if ($profile->unsetWebhooks()) {
            return $this->result->setSuccess(true)->addMessage(t('Веб-хук успешно отключен'));;
        } else {
            return $this->result->setSuccess(false)
                ->addEMessage($profile->getErrorsStr());
        }
    }

    /**
     * Загружает новые события от Telegram
     */
    function actionHandleMessages()
    {
        $this->wrapOutput(false);
        $id = $this->url->get('id', TYPE_INTEGER);
        $profile = new Profile($id);

        if (!$profile['id']) {
            $this->e404(t('Профиль не найден'));
        }

        try {
            $result = $profile->getTelegramBot()->handleGetUpdates();
            //var_dump($result);
            if ($result->isOk()) {
                return $this->result->setSuccess(true)
                    ->setHtml(t('Сообщения успешно загружены'))
                    ->addMessage(t('Сообщения успешно загружены'));
            } else {
                throw new Exception($result->getDescription(), $result->getErrorCode());
            }
        } catch (\Exception $e) {
            return $this->result
                ->setSuccess(false)
                ->setHtml($e->getMessage())
                ->addEMessage($e->getMessage());
        }
    }
}
