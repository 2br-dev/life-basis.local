<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Model\Orm\Task;
use Ai\Model\Orm\TaskResult;
use Ai\Model\TaskResultApi;
use Ai\Model\TransformerApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Toolbar;

/**
 * Контроллер, управляющий результатами генерации данных в административной панели
 */
class TaskResultCtrl extends Crud
{
    public function __construct()
    {
        parent::__construct(new TaskResultApi());
    }

    /**
     * Помощник компановки страницы
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Результаты генерации данных'));
        $helper->setTopHelp(t('В этом разделе отображаются результаты генерации данных. Вы можете открыть каждый результат, проверить качество генерации, внести коррективнки(при необходимости) и либо применить, либо отклонить изменения. Применение изменений означает, что сгенерированные данные будут установлены у объекта (товара, категории, ...), а статус результата примет значение "Применен". Отклонение изменения установит соответствующий статус результату генерации для сохранения истории обработки результатов.'));

        $view_url = $this->router->getAdminPattern('view', [':number' => '@number', ':task_id' => '@task_id']);
        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Button($this->router->getAdminUrl(false, [], 'ai-taskctrl'), t('Вернуться к задачам'))
            ]
        ]));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('status', t('Статус'), ['Sortable' => SORTABLE_BOTH, 'href' => $view_url, 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('number', t('Номер результата'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC, 'href' => $view_url, 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('entity_id', t('ID объекта'), ['Sortable' => SORTABLE_BOTH, 'href' => $view_url, 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Userfunc('entity_title', t('Наименование объекта'), function($value, $cell) {
                    return $cell->getRow()->getEntityTitle();
                }),
                new TableType\Text('task_id', t('ID задачи'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('transformer_id', t('Транформер'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'TdAttr' => ['class' => 'cell-sgray'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id',
                        ]
                    ]),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        $helper->setBottomToolbar(new Toolbar\Element([
            'items' => [
                new Toolbar\Button\Button($this->router->getAdminUrl('apply'), t('Применить'), [
                    'attr' => [
                        'class' => 'btn btn-success btn-alt crud-post',
                        'data-confirm-text' => t('Вы действительно желаете установить сгенерированные данные для %count объекта(ов)?')
                    ]
                ]),
                new Toolbar\Button\Button($this->router->getAdminUrl('regenerate'), t('Перегенерировать'), [
                    'attr' => [
                        'class' => 'btn btn-primary btn-alt crud-post',
                        'data-confirm-text' => t('Вы действительно желаете перегенерировать данные для %count объекта(ов)?')
                    ]
                ]),
                new Toolbar\Button\Button($this->router->getAdminUrl('cancel'), t('Отклонить'), [
                    'attr' => [
                        'class' => 'btn btn-danger btn-alt crud-post',
                        'data-confirm-text' => t('Вы действительно желаете отметить отклоненными %count результата(ов) генерации?')
                    ]
                ])
            ]
        ]));

        //Параметры фильтра
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('task_id', '№ задачи'),
                        new Filter\Type\Text('number', 'Порядковый номер'),
                        new Filter\Type\Text('entity_id', 'ID объекта'),
                        new Filter\Type\Select('status', t('Статус'), TaskResult::getStatusTitles([
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

        $helper->viewAsTable();

        return $helper;
    }

    /**
     * Формирует скелет страницы просмотра результата генерации
     *
     * @return CrudCollection
     */
    public function helperView()
    {
        $helper = new CrudCollection($this, $this->getApi());
        $helper->setTopTitle(t('Просмотр результата {number_of}'));
        $helper->viewAsForm();

        return $helper;
    }

    /**
     * Просмотр результата генерации
     *
     * @return Standard
     */
    public function actionView()
    {
        $task_id = $this->url->get('task_id', TYPE_INTEGER);
        $number = $this->url->get('number', TYPE_INTEGER);

        $helper = $this->getHelper();

        $task_result = TaskResult::loadByWhere([
            'task_id' => $task_id,
            'number' => $number
        ]);

        if (!$task_result['id']) {
            $this->e404();
        }

        $task_result->initTaskFields();
        $this->getApi()->setElement($task_result);

        $task = $task_result->getTask();
        $form_object = $task_result->getResultFormObject();

        if ($this->url->isPost()) {
            $sub_action = $this->url->get('subaction', TYPE_STRING);
            if ($form_object->checkData()) {
                if (($sub_action == 'apply' && $task_result->approve($form_object->getValues()))
                    || ($sub_action == 'cancel' && $task_result->cancel()))
                {
                    $this->result->setSuccess(true);
                    if ($number < $task['total_count']) {
                        //Возвращаем следующий результат
                        $number++;
                        $task_result = TaskResult::loadByWhere([
                            'task_id' => $task_id,
                            'number' => $number
                        ]);
                        $form_object = $task_result->getResultFormObject();
                    } else {
                        //Закрываем окно
                        return $this->result;
                    }
                }
            }
        }

        $this->view->assign([
            'form_object' => $form_object,
            'task_result' => $task_result
        ]);

        $helper->setForm($this->view->fetch('%ai%/admin/taskresult/view.tpl'));
        $helper->setBottomToolbar($this->getViewButtons($number, $task, $task_result));

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Возвращает кнопки для нижнего тулбара диалога просмотра результата генерации данных
     *
     * @param mixed $number
     * @param Task $task
     * @param TaskResult $task_result
     * @return Toolbar\Element
     */
    private function getViewButtons(mixed $number, Task $task, TaskResult $task_result)
    {
        $bottom_toolbar = new Toolbar\Element();
        if ($number > 1) {
            $bottom_toolbar->addItem(new Toolbar\Button\Button($this->router->getAdminUrl('view', [
                'task_id' => $task['id'],
                'number' => $number - 1
            ]), t('Назад'), [
                'attr' => [
                    'class' => 'crud-edit crud-replace-dialog'
                ]
            ]));
        }

        if ($task_result->canApprove()) {
            $bottom_toolbar->addItem(new Toolbar\Button\SaveForm('', t('Применить'), [
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('view', [
                        'task_id' => $task['id'],
                        'number' => $number,
                        'subaction' => 'apply'
                    ])
                ]
            ]));
        }

        if ($task_result->canCancel()) {
            $bottom_toolbar->addItem(new Toolbar\Button\SaveForm('', t('Отклонить'), [
                'attr' => [
                    'class' => 'btn-danger',
                    'data-url' => $this->router->getAdminUrl('view', [
                        'task_id' => $task['id'],
                        'number' => $number,
                        'subaction' => 'cancel'
                    ])
                ]
            ]));
        }

        if ($number < $task['total_count']) {
            $bottom_toolbar->addItem(new Toolbar\Button\Button($this->router->getAdminUrl('view', [
                'task_id' => $task['id'],
                'number' => $number + 1
            ]), t('Далее'), [
                'attr' => [
                    'class' => 'crud-edit crud-replace-dialog'
                ]
            ]));
        }

        return $bottom_toolbar;
    }

    /**
     * Применяет один результат генерации
     *
     * @return Standard
     */
    public function actionApply()
    {
        $chk = $this->url->post('chk', TYPE_ARRAY);
        $ids = $this->modifySelectAll($chk);
        $offset = $this->url->post('offset', TYPE_INTEGER, 0);

        $result = $this->api->apply($ids, $offset);
        if (is_numeric($result)) {
            $this->result
                ->setSuccess(true)
                ->addSection([
                        'repeatPost' => true,
                        'queryParams' => [
                            'offset' => $result,
                        ]]
                );
        } elseif ($result === true) {
            $this->result
                ->setSuccess(true)
                ->addMessage(t('Выбранные результаты успешно применены'));
        } elseif ($result === false) {
            $this->result
                ->setSuccess(false)
                ->addEMessage($this->api->getErrorsStr());
        }

        return $this->result;
    }

    /**
     * Отклоняет один результат генерации
     *
     * @return Standard
     */
    public function actionCancel()
    {
        $chk = $this->url->post('chk', TYPE_ARRAY);
        $ids = $this->modifySelectAll($chk);
        $offset = $this->url->post('offset', TYPE_INTEGER, 0);

        $result = $this->api->cancel($ids, $offset);
        if (is_numeric($result)) {
            $this->result
                ->setSuccess(true)
                ->addSection([
                    'repeatPost' => true,
                    'queryParams' => [
                        'offset' => $result,
                    ]]
                );
        } elseif ($result === true) {
            $this->result
                ->setSuccess(true)
                ->addMessage(t('Выбранные результаты успешно отменены'));
        } elseif ($result === false) {
            $this->result
                ->setSuccess(false)
                ->addEMessage($this->api->getErrorsStr());
        }

        return $this->result;
    }

    /**
     * Устанавливает статус для повторной генерации некоторых результатов
     *
     * @return Standard
     */
    public function actionRegenerate()
    {
        $chk = $this->url->post('chk', TYPE_ARRAY);
        $ids = $this->modifySelectAll($chk);

        if ($this->getApi()->regenerate($ids)) {
            return $this->result
                ->setSuccess(true)
                ->addMessage(t('Объекты отмечены для повторной генерации'));
        }
    }
}