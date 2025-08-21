<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Model\Orm\Task;
use Ai\Model\TaskApi;
use Ai\Model\Transformer\AbstractTransformer;
use Ai\Model\TransformerApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\HashStore\Api as HashStoreApi;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button\Button;
use RS\Html\Toolbar\Button\SaveForm;
use RS\Html\Toolbar\Element;

class TaskCtrl extends Crud
{
    const HASHSTORE_SKIP_WELCOME_KEY = 'ai_skip_welcome_';

    private AbstractTransformer $transformer;
    private $ids = [];
    private $is_select_all_pages;

    public function __construct()
    {
        parent::__construct(new TaskApi());
    }

    /**
     * Помощник компановки страницы
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Задачи на генерацию данных'));
        $helper->setTopHelp(t('В этом разделе отображаются все задачи на генерацию контента. Добавить задачу вы можете выделив соответствующие объекты(Товары, Категории, Новости, Пункты меню, ...) в списках в собственных разделах этих объектов.'));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Button($this->router->getAdminUrl(false, [], 'ai-taskresultctrl'), t('Перейти к результатам'))
            ]
        ]));
        $view_url = $this->router->getAdminPattern('view', [':id' => '@id']);
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH, 'href' => $view_url, 'LinkAttr' => ['class' => 'crud-edit'],'CurrentSort' => SORTABLE_DESC]),
                new TableType\Datetime('date_of_create', t('Дата создания'), ['Sortable' => SORTABLE_BOTH, 'href' => $view_url, 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Datetime('date_of_update', t('Дата обновления'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('transformer_id', t('Транформер'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('status', t('Статус'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('total_count', t('Кол-во объектов'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('generated_count', t('Всего обработано'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('errors_count', t('Есть ошибки'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('approved_count', t('Принято'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('skipped_count', t('Отклонено'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('view', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id',
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Перейти к результатам'),
                            'attr' => [
                                '@href' => $this->router->getAdminPattern(false, [':f[task_id]' => '~field~'], 'ai-taskresultctrl'),
                            ]
                        ],
                        [
                            'title' => t('Запустить заново'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('restartTask', [':id' => '~field~']),
                                'data-confirm-text' => t('Вы действительно желаете перезапустить генерацию данных по задаче?')
                            ]
                        ]
                    ])
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        //Параметры фильтра
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('id', '№', ['Attr' => ['size' => 4]]),
                        new Filter\Type\Select('status', t('Статус'), Task::getStatusTitles([
                            '' => t('- Любой -')
                        ])),
                        new Filter\Type\Select('transformer_id', t('Трансформер'), TransformerApi::staticSelectList([
                            '' => t('- Любой -')
                        ])),
                    ]])
                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        $helper->setBottomToolbar($this->buttons(['delete']));
        $helper->viewAsTable();

        return $helper;
    }

    /**
     * Просмотр одной задачи
     *
     * @return Standard
     */
    public function actionView()
    {
        $task_id = $this->url->get('id', TYPE_INTEGER);
        $task = new Task($task_id, false);

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Просмотр задачи на генерацию №{id}'), [
            'id' => $task['id']
        ]);
        $helper->viewAsForm();
        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Cancel('', t('Закрыть'))
            ]
        ]));

        $this->view->assign([
            'task' => $task
        ]);

        $helper->setForm($this->view->fetch('%ai%/admin/task/view.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Перезапускает задачу
     *
     * @return Standard
     */
    public function actionRestartTask()
    {
        $task_id = $this->url->get('id', TYPE_INTEGER);
        $task = new Task($task_id);

        if ($task->restart()) {
            $this->result
                ->setSuccess(true)
                ->addMessage(t('Задача успешно перезапущена'));
        } else {
            $this->result
                ->setSuccess(false)
                ->addEMessage($task->getErrorsStr());
        }

        return $this->result;
    }

    /**
     * Останавливает выполнение задачи
     *
     * @return Standard
     */
    public function actionStopTask()
    {
        $task_id = $this->url->get('id', TYPE_INTEGER);
        $task = new Task($task_id);

        if ($task->stop()) {
            $this->result->setSuccess(true);
            return $this->actionView();
        } else {
            $this->result
                ->setSuccess(false)
                ->addEMessage($task->getErrorsStr());
        }

        return $this->result;
    }

    /**
     * Инициализирует трансформер
     *
     * @return void
     */
    protected function initTransformer()
    {
        $transformer_id = $this->url->request('transformer_id', TYPE_STRING);
        $this->transformer = AbstractTransformer::getTransformerById($transformer_id);
        $this->is_select_all_pages = $this->url->get('selectAll', TYPE_STRING) === 'on';

        $this->ids = $this->url->get('chk', TYPE_ARRAY);
    }

    /**
     * Отображает мастер заполнения данных
     *
     * @return Standard
     */
    public function actionWizardWelcome()
    {
        $this->initTransformer();

        $skip = $this->url->get('skip', TYPE_STRING);
        $skip_welcome_stored_value = HashStoreApi::get(self::HASHSTORE_SKIP_WELCOME_KEY.$this->user->id);
        if ($skip == 'auto') {
            if ($skip_welcome_stored_value) {
                return $this->actionWizardSettings(true);
            }
        }

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Мастер заполнения данных через ИИ'));
        $helper->setBottomToolbar(new Element([
            'Items' => [
                new SaveForm($this->router->getAdminUrl('wizardWelcome', [
                    'transformer_id' => $this->transformer->getId(),
                    'chk' => $this->ids
                ]), t('Далее'), [
                    'attr' => [
                        'class' => 'btn btn-default crud-form-save crud-replace-dialog',
                    ]
                ])
            ]
        ]));

        if ($this->url->isPost()) {
            $skip_welcome = $this->url->post('skip_welcome', TYPE_INTEGER, 0);
            HashStoreApi::set(self::HASHSTORE_SKIP_WELCOME_KEY.$this->user->id, $skip_welcome);

            $this->result->setSuccess(true);
            return $this->actionWizardSettings(true);
        }

        $helper->viewAsForm();
        $this->view->assign([
            'skip_welcome' => $skip_welcome_stored_value
        ]);
        $helper->setForm($this->view->fetch('%ai%/admin/task/wizard/welcome.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Отображает шаг настройки параметров массовой генерации
     *
     * @return Standard
     */
    public function actionWizardSettings($from_welcome = false)
    {
        $this->initTransformer();

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Мастер заполнения данных через ИИ'));
        $helper->viewAsForm();
        $toolbar = new Element([
            'Items' => [
                new Button($this->router->getAdminUrl('wizardWelcome', [
                    'transformer_id' => $this->transformer->getId(),
                    'chk' => $this->ids
                ]), t('Назад'), [
                    'attr' => [
                        'class' => 'btn btn-default crud-add crud-replace-dialog',
                    ]
                ])
            ]
        ]);

        $fields = $this->transformer->getFields(true);
        if ($fields) {
            $toolbar->addItem(new SaveForm($this->router->getAdminUrl('wizardSettings', [
                'transformer_id' => $this->transformer->getId(),
                'chk' => $this->ids
            ]), t('Создать задачу на генерацию данных'), [
                'attr' => [
                ]
            ]));
        }

        $helper->setBottomToolbar($toolbar);

        if ($this->url->isPost() && !$from_welcome) {
            $settings = $this->url->post('settings', TYPE_ARRAY, []);

            //Создаем задачу на обновление объектов
            $task = new Task();
            $task['user_id'] = $this->user->id;
            $task['transformer_id'] = $this->transformer->getId();
            $task['entity_ids'] = $this->transformer->modifySelectAll($this->ids, $this->is_select_all_pages);
            $task->setSettings($settings);

            if ($task->validate() && $task->insert()) {
                return $this->result
                    ->setSuccess(true)
                    ->addSection([
                        'callCrudAdd' => $this->router->getAdminUrl('view', [
                            'id' => $task['id']
                        ], 'ai-taskctrl')
                    ]);
            } else {
                return $this->result
                    ->setSuccess(false)
                    ->setErrors($task->getDisplayErrors());
            }
        }

        $this->view->assign([
            'transformer' => $this->transformer,
            'fields' => $fields
        ]);
        $helper->setForm($this->view->fetch('%ai%/admin/task/wizard/settings.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }
}