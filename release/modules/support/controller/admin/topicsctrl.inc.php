<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Html\Category;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use Support\Model\Api;
use Support\Model\CrawlerProfileApi;
use Support\Model\Filtertype\Message;
use Support\Model\Orm\Topic;
use Support\Model\Platform\Manager;
use Support\Model\Platform\PlatformSite;
use Support\Model\TopicApi;
use Support\Model\TopicFilterApi;

class TopicsCtrl extends Crud
{
    protected $api;
    protected $topic_filters;
    protected $dir;

    public function __construct()
    {
        parent::__construct(new TopicApi);

        $this->setCategoryApi(new TopicFilterApi(), t('выборку'));
        $this->topic_filters = $this->url->get('f', TYPE_ARRAY);
        $this->dir = $this->url->get('dir', TYPE_INTEGER, 0);
    }
    
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->viewAsTableCategory();
        $helper->setTopHelp(t('Это внутренняя поддержка для пользователей вашего сайта. Авторизованные пользователи могут создавать тему обращения и вести в ней переписку с администраций сайта (с Вами). В ReadyScript предусмотрены уведомления о поступлении новых сообщений для администратора и клиентов. Воспользуйтесь <a href="//readyscript.ru/downloads-desktop/" class="u-link">Desktop приложением ReadyScript</a>, чтобы не упустить ни одного обращения клиента.'));
        $helper->setTopTitle(t('Поддержка'));
        $view_href = $this->router->getAdminPattern(null, [':id' => '@id'], 'support-supportctrl').'#answer';

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('number', '№', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH, 'href' => $view_href]),
                new TableType\Usertpl('status', t('Статус'), '%support%/admin/column/status.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('title', t('Тема'), [
                         'Sortable' => SORTABLE_BOTH, 
                         'href' => $view_href
                    ]
                ),
                new TableType\Userfunc('user_id', t('Пользователь'), function($value, $cell) {
                    return $cell->getRow()->getUserName();
                },[
                         'Sortable' => SORTABLE_BOTH
                ]),
                new TableType\User('manager_id', t('Менедждер'), [
                    'Sortable' => SORTABLE_BOTH,
                    'hidden' => true
                ]),
                new TableType\Text('created', t('Создано'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('updated', t('Обновлено'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Text('platform', t('Платформа'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('newadmcount', t('Новых сообщений'), ['TdAttr' => ['align' => 'center'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('comment', t('Комментарий'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('id', 'ID', ['Sortable' => SORTABLE_BOTH, 'tdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]]),

                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                )
            ]]
        ));    

        //Инициализируем фильтр
        $filter_control = new Filter\Control([
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('number', t('Номер'), ['SearchType' => '%like%']),
                        new Filter\Type\Select('status', t('Статус'), Topic::getStatusesTitles(['' => t('- Не важно -')])),
                        new Filter\Type\Select('platform', t('Платформа'), Manager::getPlatfromTitles(true, ['' => t('Не выбрано')])),
                        new Filter\Type\Text('title', t('Тема'), ['SearchType' => '%like%']),
                        new Message('message', t('Сообщение'), ['SearchType' => '%like%']),
                        new Filter\Type\User('user_id', t('Пользователь')),
                        new Filter\Type\Text('user_name', t('Имя пользователя (без регистрации)')),
                        new Filter\Type\Text('user_email', t('Email пользователя (без регистрации)')),
                        new Filter\Type\User('manager_id', t('Менеджер')),
                        new Filter\Type\Text('comment', t('Комментарий'), ['SearchType' => '%like%']),
                    ]
                    ])
                ],
                'SecContainers' => [
                    new Filter\Seccontainer([
                        'Lines' => [
                            new Filter\Line( [
                                'Items' => [
                                    new Filter\Type\DateRange('created', t('Дата создания')),
                                    new Filter\Type\DateRange('updated', t('Дата обновления'))
                                ]
                            ])
                        ]
                    ])
                ]
            ]),
            'Caption' => t('Поиск по тикетам')
        ]);

        $helper->setFilter($filter_control);
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));

        $crawler_api = new CrawlerProfileApi();
        $crawler_api->setFilter('is_enable', 1);
        $crawlers_count = $crawler_api->getListCount();
        if ($crawlers_count) {
            $helper->getTopToolbar()->addItem(
                new ToolbarButton\Button($this->router->getAdminUrl('fetchMail', [], 'support-crawlerctrl'),
                    t('Проверить почту'), ['attr' => ['class' => 'crud-get']]));
        }

        $_this = $this;
        $helper->setCategoryListFunction('getCategoryList');
        $helper->setCategory(new Category\Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->dir,
            'noExpandCollapseButton' => true,
            'rootItem' => [
                'id' => 0,
                'title' => t('Все темы'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noRedMarker' => true
            ],
            'sortable' => true,
            'sortUrl' => $this->router->getAdminUrl('categoryMove'),
            'mainColumn' => new TableType\Text('title', t('Сохраненные выборки'), ['href' => function ($row) use ($_this) {
                return $_this->router->getAdminUrl(false, ['dir' => $row['id'], 'f' => $row['filters_arr'], 'c' => $_this->url->get('c', TYPE_ARRAY)]);
            }]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('categoryEdit', [':id' => '~field~']), null, [
                    'class' => 'crud-sm-dialog crud-edit',
                    'attr' => [
                        '@data-id' => '@id',
                    ]]),
            ]),
            'headButtons' => [
                [
                    'text' => t('Сохраненные выборки'),
                    'tag' => 'span',
                    'attr' => [
                        'class' => 'lefttext'
                    ]
                ],
                [
                    'attr' => [
                        'title' => t('Сохранить текущую выборку задач'),
                        'href' => $this->router->getAdminUrl('categoryAdd', ['f' => $this->topic_filters]),
                        'class' => 'add crud-add crud-sm-dialog'
                    ]
                ],
            ],
        ]), $this->getCategoryApi());

        $helper->setCategoryBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Delete(null, null, [
                    'attr' => ['data-url' => $this->router->getAdminUrl('categoryDel')]
                ]),
            ]
        ]));

        $helper->setCategoryFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->getCategoryApi()->defAlias()],
            'filterVar' => 'c',
            'Caption' => t('Поиск по выборкам')
        ]));

        return $helper;
    }
    
    public function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $helper = $this->getHelper();
        /**
         * @var $topic Topic
         */
        $topic = $this->api->getElement();

        if ($primaryKey === null) {
            $topic->_admin_creation_ = true;
            $topic->platform = PlatformSite::PLATFORM_ID;
            $topic->___first_message_->setVisible(true);
            $helper->setTopTitle(t('Создать новый тикет'));
        } else {
            $helper->setTopTitle(t('Редактировать тикет {number}'));
        }

        $helper->setFormSwitch($topic->platform);

        $result = parent::actionAdd($primaryKey, $returnOnSuccess, $helper);

        if ($primaryKey === null) {
            if ($result->isSuccess()) {
                $result->setAjaxWindowRedirect($this->router->getAdminUrl(false, ['id' => $this->api->getElement()->id], 'support-supportctrl'));
            }
        }
        return $result;
    }

    /**
     * Формирует хелпер для создания выборки
     *
     * @return CrudCollection
     */
    public function helperCategoryAdd()
    {
        $this->api = $this->getCategoryApi();

        $helper = new CrudCollection($this, $this->getCategoryApi());
        $helper->viewAsForm();
        $helper->setTopTitle(t('Сохранить выборку'));
        $helper->setBottomToolbar($this->buttons(['save', 'cancel']));

        return $helper;
    }

    /**
     * Открывает окно сохранения текущей выборки товаров
     *
     * @param int $primaryKey - id выборки
     * @return Standard
     */
    public function actionCategoryAdd($primaryKey = null)
    {
        if (!$primaryKey && !$this->topic_filters) {
            return $this->result->setSuccess(false)->addSection('close_dialog', true)
                ->addEMessage(t('Установите хотя бы один фильтр для сохранения выборки'));
        }

        $helper = $this->getHelper();

        if ($this->url->isPost()) {
            $user_post = [
                'user_id' => $this->user['id']
            ];

            if (!$primaryKey) {
                $user_post['filters_arr'] = $this->topic_filters;
            }

            if ($this->getCategoryApi()->save($primaryKey, $user_post)) {
                return $this->result->setSuccess(true);
            } else {
                return $this->result->setSuccess(false)->setErrors($this->getCategoryApi()->getElement()->getDisplayErrors());
            }
        }

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Формирует хелпер для формирования окна редактирования выборки
     *
     * @return CrudCollection
     */
    public function helperCategoryEdit()
    {
        $helper = $this->helperCategoryAdd();
        $helper->setTopTitle(t('Переименовать выборку'));
        return $helper;
    }

    /**
     * Изменяет статус темы переписки
     *
     * @return Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    public function actionAjaxChangeStatus()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $status = $this->url->convert($this->url->get('status', TYPE_STRING),
            array_keys(Topic::getStatusesTitles()));

        $topic = new Topic($id);

        if ($topic['id']) {
            $topic['status'] = $status;
            if ($topic->update()) {
                return $this->result->setSuccess(true);
            } else {
                return $this->result->setSuccess(false)
                    ->addEMessage($topic->getErrorsStr());
            }
        }

        $this->e404(t('Тема не найдена'));
    }

    /**
     * Изменяет менеджера тикета
     *
     * @return Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    public function actionAjaxChangeManager()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $support_api = new Api();
        $user_id = $this->url->convert($this->url->get('manager_id', TYPE_STRING),
            array_merge([0], array_keys($support_api->getManagers())));

        $topic = new Topic($id);

        if ($topic['id']) {
            $topic['manager_id'] = $user_id;
            if ($topic->update()) {
                return $this->result->setSuccess(true);
            } else {
                return $this->result->setSuccess(false)
                    ->addEMessage($topic->getErrorsStr());
            }
        }

        $this->e404(t('Тема не найдена'));
    }

    /**
     *
     */
    public function actionEditComment()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $topic = new Topic($id);
        if (!$topic['id']) {
            $this->e404(t('Тикет не найден'));
        }

        $form_object = new FormObject(new PropertyIterator([
            'comment' => (new Type\Text)
                ->setDescription(t('Комментарий администратора'))
                ->setHint(t('Не будет виден клиенту'))
        ]));

        $form_object['comment'] = $topic['comment'];

        if ($this->url->isPost()) {
            if ($form_object->checkData()) {
                $topic['comment'] = $form_object['comment'];
                $topic->update();
                return $this->result->setSuccess(true);
            } else {
                return $this->result
                    ->setSuccess(false)
                    ->setErrors($form_object->getDisplayErrors());
            }
        }

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Комментарий администратора'));
        $helper->setBottomToolbar($this->buttons(['save', 'cancel']));
        $helper->viewAsForm();
        $helper->setFormObject($form_object);

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Удаляет тикет
     *
     * @return mixed
     */
    public function actionDel()
    {
        $result = parent::actionDel();
        if ($result instanceof Standard) {
            if ($result->isSuccess()) {
                $redirect_to_topic = $this->url->get('redirect_to_topic', TYPE_INTEGER);
                if ($redirect_to_topic) {
                    $result
                        ->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name . 'index', '?'))
                        ->addSection('noUpdate', true);
                }
            }
        }

        return $result;
    }


}

