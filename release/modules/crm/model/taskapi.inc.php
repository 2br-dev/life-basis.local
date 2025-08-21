<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Config\ModuleRights;
use Crm\Model\FilterType\Collaborators;
use Crm\Model\FilterType\Observers;
use Crm\Model\Orm\Task;
use Crm\Model\Orm\TaskFilter;
use Crm\Model\Orm\UserLink;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Helper\Tools;
use \RS\Html\Filter;
use RS\Orm\Request;

/**
 * Класс для организации выборок ORM объекта
 */
class TaskApi extends AbstractLinkedApi
{
    protected
        $implementer_user_id_field = 'implementer_user_id';

    function __construct()
    {
        parent::__construct(new Orm\Task(), [
            'defaultOrder' => 'date_of_create DESC',
            'sortField' => 'board_sortn',
            'loadOnDelete' => true
        ]);
    }

    /**
     * Возвращает структуру фильтра для фильтрации задач
     *
     * @return Filter\Control
     */
    public function getFilterControl()
    {
        $field_manager = Loader::byModule($this)->getTaskUserFieldsManager();

        return new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('task_num', t('Номер'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('title', t('Короткое описание'), ['SearchType' => '%like%']),
                        new Filter\Type\User('creator_user_id', t('Создатель')),
                        new Collaborators('implementer_user_id', UserLink::SOURCE_TYPE_TASK, t('Исполнитель')),
                        new Observers('observer_user_id', UserLink::SOURCE_TYPE_TASK, t('Наблюдатель')),
                        new Filter\Type\Select('status_id', t('Статус'), StatusApi::staticSelectList(['' => t('Любой')], 'crm-task')),
                        new Filter\Type\Select('type_id', t('Тип задачи'), TaskTypeApi::staticSelectList(['' => t('Любой')])),
                        new Filter\Type\Select('is_archived', t('Архивная?'), ['' => t('Не важно'), '0' => t('Нет'), '1' => t('Да')]),
                        new Filter\Type\DateRange('date_of_create', t('Дата создания')),
                        new Filter\Type\DateRange('date_of_planned_end', t('План окончания')),
                        new Filter\Type\DateRange('date_of_end', t('Дата завершения')),
                        new \Crm\Model\FilterType\CustomFields('custom_fields', $field_manager, $this->getElement()->getShortAlias()),
                        new \Crm\Model\FilterType\Links('links', Task::getAllowedLinkTypes(), Task::getLinkSourceType())
                    ]
                    ])
                ],
            ]),
            'ExcludeGetParams' => ['dir'],
            'Caption' => t('Поиск по задачам')
        ]);
    }

    /**
     * Возвращает количество записей в выборке
     *
     * @return int
     */
    public function getListCount()
    {
        $q = clone $this->queryObj();
        $q->orderby(false);

        $q->groupby(false)->select = 'COUNT(DISTINCT A.id) cnt';
        $count = $q->exec()->getOneField('cnt', 0);

        return $count;
    }

    /**
     * Применяет сохраненный фильтр
     *
     * @param TaskFilter $filter
     */
    public function applyFilter(TaskFilter $filter)
    {
        $filter_control = $this->getFilterControl();
        $filter_control->fill($filter['filters_arr'] ?: []);

        $this->addFilterControl($filter_control);
    }

    /**
     * Добавляет фильтр, который исключает архивные задачи
     *
     * @return void
     */
    public function excludeArchivedItems()
    {
        $this->setFilter('is_archived', 0);
    }

    /**
     * Устанавливает фильтры, которые соответствуют правам текущего пользователя
     */
    function initRightsFilters()
    {
        //Если у пользователя нет прав на просмотр чужих объектов, то не отображаем их.
        $user = Auth::getCurrentUser();

        if (!Rights::hasRight($this, ModuleRights::TASK_OTHER_READ)) {
            $filters = [
                //Отображаем только те объекты, которые мы создали
                $this->creator_user_id_field => $user['id']
            ];

            //Или те объекты, которые нам назначены
            $filters['|' . $this->implementer_user_id_field] = $user['id'];

            //Или те, где мы являемся наблюдателем или соисполнителем
            $this->leftJoinUserRolesTable([
                UserLink::USER_ROLE_COLLABORATOR,
                UserLink::USER_ROLE_OBSERVER,
            ], $user['id']);

            $filters['|UL0.user_id:is'] = 'NOT NULL';

            $this->setFilter([$filters]);
        }
    }

    /**
     * Устанавливает фильтр по пользователям
     *
     * @param array $roles
     * @param integer $user_id
     * @return $this
     */
    function leftJoinUserRolesTable($roles, $user_id)
    {
        $this->queryObj()
            ->leftjoin(new UserLink(), "UL0.source_id = A.id AND UL0.source_type = '".UserLink::SOURCE_TYPE_TASK."' AND UL0.user_role IN (" . implode(',', Tools::arrayQuote($roles)).") AND UL0.user_id = " . $user_id, 'UL0');

        return $this;
    }

    /**
     * Возвращает задачи, по которым возможно необходимо отправить уведомление о предстоящем завершении
     *
     * @return integer Возвращает количество задач, для которых было отправлено уведомление
     */
    function sendTaskNotice()
    {
        $config = Loader::byModule($this);

        $times = $config->getNoticeExpirationTimeList();
        end($times);
        $max_interval = key($times);
        $current_time = time();
        $page_size = 50;

        //Выбираем задачи, которые могут истечь в ближайшее время
        $q = Request::make()
                ->from(new Task())
                ->limit($page_size)
                ->where("expiration_notice_time != 0 
                        AND date_of_planned_end <= '#max_interval' 
                        AND date_of_planned_end > '#current_time'
                        AND implementer_user_id > 0",
                    [
                        'current_time' => date('Y-m-d H:i:s', $current_time),
                        'max_interval' => date('Y-m-d H:i:s', $current_time + $max_interval)
                    ]);

        $need_statuses = (array)$config->expiration_task_notice_statuses;
        if ($need_statuses && !in_array(0, $need_statuses)) {
            $q->whereIn('status_id', $need_statuses);
        }

        $count = 0;
        $offset = 0;
        while($tasks = $q->offset($offset)->objects()) {
            foreach($tasks as $task) {
                if ($task->isTimeToExpire()) {
                    $task->sendExpireNotice();
                    $count++;
                }
            }
            $offset += $page_size;
        }

        return $count;
    }
}
