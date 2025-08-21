<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\TaskTypeApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Table\Type as TableType;
use RS\Html\Table;

/**
 * Контроллер управления списком типов задач
 */
class TaskTypeCtrl extends Crud
{
    protected $api;

    public function __construct()
    {
        parent::__construct(new TaskTypeApi());
    }

    /**
     * Формирует хелпер для отображения списка задач
     *
     * @return CrudCollection
     */
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle('Типы задач');
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить тип задачи')]));
        $helper->addCsvButton('crm-tasktype');
        $helper->setTopHelp(t('Тип задачи - это условный текстовый идентификатор, который далее может определять, какие правила автоматизации будет применены к задаче. Типы задач являются общими для всех мультисайтов.'));

        $helper->setTable(new Table\Element([
            'Columns' => array_merge(
                [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),

                    new TableType\User('creator_user_id', t('Создатель'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\User('implementer_user_id', t('Исполнитель'), ['Sortable' => SORTABLE_BOTH]),
                ],
            )]));

        $helper->setFilter($this->api->getFilterControl());

        return $helper;
    }
}
