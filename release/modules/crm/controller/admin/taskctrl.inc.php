<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Config\ModuleRights;
use Crm\Model\CallHistoryApi;
use Crm\Model\ChatHistoryApi;
use Crm\Model\Orm\ChatHistory;
use Crm\Model\Orm\Task;
use Crm\Model\Orm\TaskType;
use Crm\Model\TaskApi;
use Crm\Model\TaskFilterApi;
use Crm\Model\TaskTypeApi;
use Crm\Model\View\Manager as ViewManager;
use RS\AccessControl\Rights;
use RS\Config\Loader;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Helper\Tools as HelperTools;
use RS\Html\Category;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Orm\AbstractObject;

/**
 * Контроллер управления списком задач
 */
class TaskCtrl extends Crud
{
    /** @var TaskApi */
    protected $api;
    protected $type_api;
    protected $dir;
    protected $task_filters;

    public function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new TaskApi());
        $this->api->initRightsFilters();
        $this->type_api = new TaskTypeApi();
        $this->setCategoryApi(new TaskFilterApi(), t('выборку'));

        $this->task_filters = $this->url->get('f', TYPE_ARRAY);
        $this->dir = $this->url->get('dir', TYPE_INTEGER, 0);
    }

    /**
     * Формирует хелпер для отображения списка задач
     *
     * @return CrudCollection
     */
    public function helperIndex()
    {
        $_this = $this; //Для Closure в php 5.3
        /** @var \Crm\Config\File $config */
        $config = Loader::byModule($this);
        $this->api->queryObj()->select = 'DISTINCT A.*';

        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->viewAsTableCategory();
        $helper->setTopTitle('Задачи'); //Установим заголовок раздела
        //$helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить задачу')]));
        $task_types = $this->type_api->getList();
        $task_types_dropdown_items = [[
            'title' => t('добавить задачу'),
            'attr' => [
                'href' => $this->router->getAdminUrl('add'),
                'class' => 'crud-add btn-success dropdown-show-scroll'
            ]
        ]];
        if (!empty($task_types)) {
            foreach ($task_types as $task_type) {
                $task_types_dropdown_items[] = [
                    'title' => $task_type['title'],
                    'attr' => [
                        'href' => $this->router->getAdminUrl('add', ['task_type_id' => $task_type['id']], 'crm-taskctrl'),
                        'class' => 'crud-add'
                    ]
                ];
            }
        }
        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Dropdown($task_types_dropdown_items),
            ]
        ]));
        $helper->addCsvButton('crm-task');
        $helper->getTopToolbar()
            ->addItem(
                new ToolbarButton\Button($this->router->getAdminUrl(false, ['type' => $this->api->getElement()->getShortAlias(), 'filter' => ['preset_id' => $this->dir]], 'crm-boardctrl'), t('Показать на доске'))
            )
            ->addItem(
                new ToolbarButton\Button($this->router->getAdminUrl(false, ['preset' => $this->dir], 'crm-taskgantctrl'), t('Диаграмма Ганта'))
            );

        $helper->setTopHelp(t('В данном разделе представлены задачи, которые создали Вы, либо для Вас. Информируйте о ходе выполнения задачи с помощью статусов. Здесь отображаются все задачи в системе, независимо от выбранного мультисайта.'));

        $field_manager = $config->getTaskUserFieldsManager();
        $custom_table_columns = $this->api->getCustomTableColumns($field_manager);

        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => array_merge(
                [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Text('task_num', t('Номер'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('title', t('Короткое описание'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Usertpl('status_id', t('Статус'), '%crm%/admin/table/status_id.tpl', ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Datetime('date_of_create', t('Создана'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Usertpl('date_of_planned_end', t('План завершения'), '%crm%/admin/table/date_of_planned_end.tpl', ['Sortable' => SORTABLE_BOTH,]),

                    new TableType\Datetime('date_of_end', t('Завершено'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\User('creator_user_id', t('Создатель'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\User('implementer_user_id', t('Исполнитель'), ['Sortable' => SORTABLE_BOTH]),

                    new TableType\Usertpl('checklist', t('Чек-лист'), '%crm%/admin/table/checklist.tpl'),
                    new TableType\Usertpl('links', t('Связи'), '%crm%/admin/table/links.tpl'),
                    new TableType\StrYesno('is_archived', t('Архивная')),
                ],
                $custom_table_columns,
                [
                    new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
                ]
            )]));

        $this->api->addCustomFieldsData($helper['table'], $this->api->getElement()->getShortAlias());

        //Добавим фильтр значений в таблице по названию
        $helper->setFilter($this->api->getFilterControl());

        $helper->setCategoryListFunction('getCategoryList');
        $helper->setCategory(new Category\Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->dir,
            'noExpandCollapseButton' => true,
            'rootItem' => [
                'id' => 0,
                'title' => t('Все задачи'),
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
                        'href' => $this->router->getAdminUrl('categoryAdd', ['f' => $this->task_filters]),
                        'class' => 'add crud-add crud-sm-dialog'
                    ]
                ],
            ],
        ]), $this->getCategoryApi());

        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));

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

    /**
     * Отображает список задач
     *
     * @param null $primaryKeyValue
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $link_type = $this->url->get('link_type', TYPE_STRING);
        $link_id = $this->url->get('link_id', TYPE_INTEGER);
        $from_call = $this->url->get('from_call', TYPE_INTEGER);
        $task_type_id = $this->url->get('task_type_id', TYPE_STRING);

        $helper = $this->getHelper();
        /** @var Task $element */
        $element = $this->api->getElement();

        if ($primaryKeyValue <= 0) { //Если создание сделки
            $helper->setTopTitle(t('Добавить задачу'));

            $element['task_num'] = HelperTools::generatePassword(8, range('0', '9'));
            $element['creator_user_id'] = $this->user['id'];
            $element['implementer_user_id'] = $this->user['id'];
            $element['date_of_create'] = date('Y-m-d H:i:s');
            $element->setTemporaryId();
            $element->initUserRights(AbstractObject::INSERT_FLAG);

            if ($link_type && $link_id) {
                $element['__links']->setVisible(false);
            }
        } else {
            $helper->setTopTitle(t('Редактировать задачу {title}'));
            $element->initUserRights(AbstractObject::UPDATE_FLAG);
        }

        if (!$element['autotask_group']) {
            $element->hideAutoTaskTab();
        }

        if (!$primaryKeyValue && $from_call) {
            $call_data = CallHistoryApi::getDataForTask($from_call);
            $element->getFromArray($call_data);
        }

        if ($link_type && $link_id) {
            $this->user_post_data['links'] = [
                $link_type => [$link_id]
            ];
        }

        if ($task_type_id) {
            $task_type = new TaskType($task_type_id);
            if (isset($task_type['id'])) {
                $element['type_id'] = $task_type['id'];
                if ($task_type['implementer_user_id']) {
                    $element['implementer_user_id'] = $task_type['implementer_user_id'];
                }
                if ($task_type['collaborator_users_id']) {
                    $element['collaborator_users_id'] = $task_type['collaborator_users_id'];
                }
                if ($task_type['observer_users_id']) {
                    $element['observer_users_id'] = $task_type['observer_users_id'];
                }
            }
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Групповое редактирование элементов
     *
     * @return \RS\Controller\Result\Standard
     * @throws \Exception
     * @throws \SmartyException
     */
    public function actionMultiEdit()
    {
        $element = $this->api->getElement();
        /** @var Task $element */
        $element->initUserRights(AbstractObject::UPDATE_FLAG);

        return parent::actionMultiEdit();
    }

    /**
     * Формирует хелпер для редактирования элемента
     * @return CrudCollection
     */
    public function helperEdit()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);

        $helper = parent::helperEdit();
        $helper['bottomToolbar']->addItem(
            new ToolbarButton\delete($this->router->getAdminUrl('deleteOne', ['id' => $id, 'dialogMode' => $this->url->request('dialogMode', TYPE_INTEGER)]), null, [
                'noajax' => true,
                'attr' => [
                    'class' => 'btn-alt btn-danger delete crud-get crud-close-dialog',
                    'data-confirm-text' => t('Вы действтельно хотите удалить данную задачу?')
                ]
            ]), 'delete'
        );

        return $helper;
    }


    /**
     * Редактирование элемента
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = $this->url->get('id', TYPE_INTEGER);
        $element = $this->api->getElement();
        $element->load($id);

        ViewManager::obj()
            ->setEntity($element)
            ->markAsViewed();

        $this->view->assign([
            'chat_history' => new ChatHistory(),
        ]);

        return parent::actionEdit();
    }

    /**
     * Удаляет одну задачу
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionDeleteOne()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $this->api->setFilter('id', $id);
        $reason = '';

        if ($task = $this->api->getFirst()) {
            if ($task->delete()) {

                if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
                    $this->result->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
                }
                return $this->result->setSuccess(true)
                    ->setNoAjaxRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
            } else {
                $reason = t(' Причина: %0', [$task->getErrorsStr()]);
            }
        }

        return $this->result->setSuccess(false)->addEMessage(t('Не удалось удалить задачу.') . $reason);
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
        if (!$primaryKey && !$this->task_filters) {
            return $this->result->setSuccess(false)->addSection('close_dialog', true)->addEMessage(t('Установите хотя бы один фильтр для сохранения выборки'));
        }

        $helper = $this->getHelper();

        if ($this->url->isPost()) {

            $user_post = [
                'user_id' => $this->user['id']
            ];

            if (!$primaryKey) {
                $user_post['filters_arr'] = $this->task_filters;
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
     * Добавляет сообщение в чат
     *
     * @return Standard
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function actionAjaxSendMessage()
    {
        if ($access_error = Rights::CheckRightError($this, ModuleRights::TASK_CHAT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $task_id = $this->url->request('task_id', TYPE_INTEGER);
        $message = strip_tags(trim($this->url->request('message', TYPE_STRING)));
        $reply_to_id = $this->url->request('reply_to_id', TYPE_INTEGER);
        $attachments = $this->url->request('attachments', TYPE_ARRAY);

        if (!$task_id || !$message) {
            return $this->result->setSuccess(false)->addEMessage(t('Неверные данные'));
        }

        $task = new \Crm\Model\Orm\Task($task_id);
        if (!$task['id']) {
            return $this->result->setSuccess(false)->addEMessage(t('Задача не найдена'));
        }

        $entryData = ChatHistoryApi::writeMessage($task, $message, $reply_to_id, $attachments);

        $view = new \RS\View\Engine();
        $view->assign([
            'entry' => $entryData
        ]);

        $html = $view->fetch('%crm%/form/chat/chat_entry_message.tpl');

        return $this->result->setSuccess(true)->addSection('html', $html);
    }


    /**
     * Получает новые сообщения в чате
     *
     * @return Standard
     * @throws \RS\Exception
     */
    public function actionAjaxGetNewMessages()
    {
        $task_id = $this->url->request('task_id', TYPE_INTEGER);
        $after_id = $this->url->request('after_id', TYPE_INTEGER);
        $before_id = $this->url->request('before_id', TYPE_INTEGER);
        $mark_as_viewed = $this->url->request('mark_as_viewed', TYPE_BOOLEAN);

        $task = new \Crm\Model\Orm\Task($task_id);
        if (!$task['id']) {
            return $this->result->setSuccess(false)->addEMessage(t('Задача не найдена'));
        }
        if ($mark_as_viewed) {
            $chat_history = new ChatHistory();
            ViewManager::obj()->setEntity($task)
                ->markAllAsViewed(trim(str_replace('crm-', '', $chat_history->getShortAlias())));
        }

        return $this->result->setSuccess(true)
            ->addSection('data', ChatHistoryApi::getChatHistoryByTask($task, $before_id ?: null, $after_id ?: null));
    }
}
