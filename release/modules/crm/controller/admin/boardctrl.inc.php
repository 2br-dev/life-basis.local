<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Config\ModuleRights;
use Crm\Model\BoardApi;
use Crm\Model\Orm\Status;
use Crm\Model\Orm\Task;
use Crm\Model\View\Manager as ViewManager;
use RS\Application\Auth;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Helper\Paginator;
use RS\Html\Toolbar;
use \RS\Html\Toolbar\Button as ToolbarButton;

/**
 * Контроллер Kanban-доски
 */
class BoardCtrl extends Front
{
    const
        PAGE_SIZE = 30;

    public
        $object_types,
        $current_object_type,
        $current_object,
        $filters,
        $term,
        $current_filter;

    function init()
    {
        $this->object_types = BoardApi::getAllBoardItems();

        $this->current_object_type = $this->url->convert(
            $this->url->request('type', TYPE_STRING, 'crm-task'),
            array_keys($this->object_types));

        $this->current_object = BoardApi::makeBoardItemByObjectType($this->current_object_type);

        $this->filters = $this->current_object->getFilters();
        $cookie_filters = json_decode($this->url->cookie('board_filters', TYPE_STRING, '', null), true) ?: [];

        $this->current_filter = $this->url->request('filter', TYPE_ARRAY, $cookie_filters);
        $this->term = $this->url->request('term', TYPE_STRING, null);
        $this->current_filter += $this->current_object->getDefaultFilterValues();
        $this->app->headers->addCookie('board_filters', json_encode($this->current_filter, JSON_UNESCAPED_UNICODE), time() + 60*60*24*365*10);
        $this->view->assign([
            'current_object_type' => $this->current_object_type,
            'current_object' => $this->current_object,
            'objects_types' => $this->object_types,
            'filters' => $this->filters,
            'current_filter' => $this->current_filter
        ]);
    }

    /**
     * Список объектов
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $helper = new CrudCollection($this);

        $statuses = Status::getStatusesByObjectType($this->current_object_type);

        $top_toolbar = new Toolbar\Element();

        $top_toolbar->setItems($this->current_object->getButtons($this->current_filter));
        $top_toolbar->addItem(new ToolbarButton\ModuleConfig(
            $this->router->getAdminUrl('edit', [
                'mod' => $this->mod_name
            ], 'modcontrol-control')), 'moduleconfig');

        $helper->setTopToolbar($top_toolbar);

        $helper->setTopTitle(t('Kanban доска'));
        $helper->setTopHelp(t('Доска позволяет анализировать, корректировать состояние ваших задач, сделок в разрезе различных статусов. Задачи на доске можно выстраивать по приоритету'));
        $helper->viewAsAny();

        $items_html = [];
        foreach($statuses as $status) {
            $items_html[$status['id']] = $this->actionAjaxGetElements($status['id'], 1)->getHtml();
        }

        $this->view->assign([
            'statuses' => $statuses,
            'items_html' => $items_html
        ]);

        $helper->setForm($this->view->fetch('%crm%/admin/board/board.tpl'));

        return $this->result->setTemplate( $helper->getTemplate() );
    }

    /**
     * Устанавливает прочитанными все задачи по статусу
     */
    public function actionAjaxMarkAllRead()
    {
        $status_id = $this->url->request('status_id', TYPE_INTEGER);

        $task = new Task();
        ViewManager::obj()
            ->markAllAsViewed(trim(str_replace('crm-', '', $task->getShortAlias())), $status_id);

        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает одну страницу элементов по типу объекта и статусу
     */
    function actionAjaxGetElements($status_id = null, $page = null)
    {
        if ($access_error = \RS\AccessControl\Rights::CheckRightError($this, ModuleRights::TASK_READ)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        if ($status_id === null) {
            $status_id = $this->url->get('status_id', TYPE_INTEGER);
        }

        if ($page === null) {
            $page = $this->url->get('page', TYPE_INTEGER, 1);
        }

        $api = $this->current_object->getApiWithFilters($status_id, $this->current_filter);

        if ($this->term) {
            $api->setFilter([
                '&' => [
                    'task_num:%like%' => $this->term,
                    '|title:%like%' => $this->term,
                    '|description:%like%' => $this->term,
                ],
            ]);
            $this->view->assign('term', $this->term);
        }

        $paginator = new Paginator($page, $api->getListCount(), self::PAGE_SIZE);

        $items = $api->getList($page, self::PAGE_SIZE, 'board_sortn');
        $this->view->assign([
            'items' => $items,
            'paginator' => $paginator,
            'status_id' => $status_id
        ]);

        return $this->result->setTemplate('%crm%/admin/board/items.tpl');
    }

    /**
     * Сортирует элементы
     */
    function actionAjaxSortElement()
    {
        if ($access_error = \RS\AccessControl\Rights::CheckRightError($this, ModuleRights::TASK_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $new_status_id = $this->url->get('status_id', TYPE_INTEGER);
        $from = $this->url->get('from', TYPE_INTEGER);
        $to = $this->url->get('to', TYPE_INTEGER);
        $direction = $this->url->get('direction', TYPE_STRING);

        $items_api = $this->current_object->getApi();
        $board_api = new BoardApi();
        if ($board_api->moveItem($items_api, $from, $to, $direction, $new_status_id)) {
            return $this->result->setSuccess(true);
        }

        return $this->result->setSuccess(false)->addEMessage(t('Не удалось переместить элемент'));
    }
}