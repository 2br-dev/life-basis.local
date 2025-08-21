<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Admin;

use RS\Controller\Admin\Crud;
use Support\Model\CrawlerProfileApi;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use Support\Model\Orm\CrawlerProfile;

/**
 * Контроллер профилей сборщиков писем
 */
class CrawlerCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new CrawlerProfileApi());
    }

    /**
     * Формирует состав страницы со списком профилей
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Профили сборщиков почты'));
        $helper->setTopHelp(t('Здесь вы можете управлять профилями сборки писем. Создайте почтовый ящик на вашем хостинге и настройте сборку и отправку через него писем вашей службы поддержки. Сбор писем осуществляется только по протоколу IMAP.'));

        $this->view->assign([
            'unexists_modules' => CrawlerProfileApi::getUnexistsModules()
        ]);
        $helper->setBeforeTableContent($this->view->fetch('%support%/admin/module_notice.tpl'));

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH,'LinkAttr' => ['class' => 'crud-edit'], 'href' => $this->router->getAdminPattern('edit', [':id' => '@id'])]),
                new TableType\Text('email', t('Email'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('server_host', t('Хост')),
                new TableType\Text('server_port', t('Порт')),
                new TableType\Text('server_login', t('Логин')),
                new TableType\StrYesno('is_enable', t('Включен')),
                new TableType\Text('crawl_interval_min', t('Интервал сборки, мин')),
                new TableType\Datetime('date_of_last_receive', t('Последнее получение почты')),
                new TableType\Datetime('date_of_last_send', t('Последняя отправка почты')),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Проверить прием почты'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('checkReceiveMail', [':id' => '@id']),
                            ]
                        ],
                        [
                            'title' => t('Проверить отправку почты'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('checkSendMail', [':id' => '@id']),
                            ]
                        ],
                    ])
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]
        ]));


        return $helper;
    }

    /**
     * Устанавливает задание - проверить почту при следующем запуске планировщика
     */
    function actionFetchMail()
    {
        $this->api->setNeedRunCrawlerMark();

        return $this->result
            ->setSuccess(true)
            ->addMessage(t('Задание на загрузку тикетов из почты установлено. В течение минуты, почта будет проверена.'));
    }

    /**
     * Проверяет получение писем
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionCheckReceiveMail()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $profile = new CrawlerProfile($id);
        if ($profile['id']) {
            if ($profile->checkReceive()) {
                return $this->result->setSuccess(true)
                    ->addMessage(t('Прием писем успешно работает'));
            } else {
                return $this->result->setSuccess(false)
                    ->addEMessage(t('Не удалось принять письма. Причина: %0', [$profile->getErrorsStr()]));
            }
        }

        $this->e404(t('Профиль не найден'));
    }

    /**
     * Проверяет отправку писем
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionCheckSendMail()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $profile = new CrawlerProfile($id);
        if ($profile['id']) {
            if ($profile->checkSend()) {
                return $this->result->setSuccess(true)
                    ->addMessage(t('Отправка писем успешно работает'));
            } else {
                return $this->result->setSuccess(false)
                    ->addEMessage(t('Не удалось принять письма. Причина: %0', [$profile->getErrorsStr()]));
            }
        }

        $this->e404(t('Профиль не найден'));
    }

    /**
     * Возвращает шаблон уведомления по умолчанию
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionGetDefaultTemplate()
    {
        $type = $this->url->get('type', TYPE_STRING);
        $this->wrapOutput(false);
        $template = "%support%/admin/template/{$type}.tpl";

        if ($this->view->templateExists($template)) {
            return $this->result->setSuccess(true)
                ->setTemplate($template);
        } else {
            return $this->result->setSuccess(false)
                    ->addEMessage(t('Такого шаблона не существует'));
        }
    }
}