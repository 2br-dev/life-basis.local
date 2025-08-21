<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use Alerts\Model\Manager;
use Crm\Config\ModuleRights;
use Crm\Model\AutoTaskRuleApi;
use Crm\Model\BoardApi;
use Crm\Model\ChatHistoryApi;
use Crm\Model\CheckListGroupApi;
use Crm\Model\Links\LinkManager;
use Crm\Model\Links\Type\AbstractType;
use Crm\Model\Links\Type\LinkTypeCall;
use Crm\Model\Links\Type\LinkTypeDeal;
use Crm\Model\Links\Type\LinkTypeUser;
use Crm\Model\Notice\ChangeTaskToUser;
use Crm\Model\Notice\NewTaskToImplementer;
use Crm\Model\Notice\TaskSoonExpireToImplementer;
use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\TaskApi;
use Files\Model\FileApi;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Event\Manager as EventManager;
use Crm\Model\View\Manager as ViewManager;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use Users\Model\Orm\User;

/**
 * ORM объект - "задача"
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $task_num Уникальный номер задачи
 * @property array $links Связь с другими объектами
 * @property string $title Суть задачи
 * @property integer $status_id Статус
 * @property string $description Описание
 * @property string $date_of_create Дата создания
 * @property string $date_of_planned_end Планируемая дата завершения задачи
 * @property string $date_of_end Фактическая дата завершения задачи
 * @property integer $expiration_notice_time Уведомить исполнителя о скором истечении срока выполнении задачи за...
 * @property integer $creator_user_id Создатель задачи
 * @property integer $implementer_user_id Исполнитель задачи
 * @property array $collaborator_users_id Соисполнители
 * @property array $observer_users_id Наблюдатели
 * @property integer $board_sortn Сортировочный индекс на доске
 * @property integer $autotask_index Порядковый номер автозадачи
 * @property integer $autotask_group Идентификатор группы связанных заказов
 * @property integer $is_autochange_status Включить автосмену статуса
 * @property array $autochange_status_rule_arr Условия для смены статуса
 * @property string $autochange_status_rule Условия для смены статуса
 * @property integer $expiration_notice_is_send Было ли отправлено уведомление об истечении срока выполнения задачи?
 * @property integer $is_archived Задача архивная?
 * --\--
 */
class Task extends OrmObject
{
    const
        PLANNED_END_STATUS_BLACK = 'black',
        PLANNED_END_STATUS_ORANGE = 'orange',
        PLANNED_END_STATUS_RED = 'red',
        PLANNED_END_STATUS_GREEN = 'green',

        ORANGE_STATUS_DAYS = 2,

        FILES_LINK_TYPE = 'Crm-CrmTask';

    protected static
        $table = 'crm_task';

    protected $before;

    function _init()
    {
        $config = Loader::byModule(__CLASS__);
        $router = \RS\Router\Manager::obj();

        parent::_init()->append([
            t('Основные'),
                'task_num' => new Type\Varchar([
                    'description' => t('Уникальный номер задачи'),
                    'hint' => t('Может использоваться для быстрой идентификации задачи внутри компании'),
                    'maxLength' => 20,
                    'unique' => true,
                    'meVisible' => false,
                ]),
                'links' => new \Crm\Model\OrmType\Link([
                    'description' => t('Связь с другими объектами'),
                    'allowedLinkTypes' => [self::getAllowedLinkTypes()],
                    'linkSourceType' => self::getLinkSourceType(),
                    'hint' => t('После связывания с другими объектами, вы сможете найти данную задачу прямо в карточках привязанных объектов'),
                    'compare' => function($value, $property, $orm) {
                        //Функция, возвращающая строковое значение данного поля для сравнения изменений
                        $result = [];
                        if ($value) {
                            foreach ($value as $link_type => $link_ids) {
                                foreach ($link_ids as $id) {
                                    $link = AbstractType::makeById($link_type);
                                    $link->init($id);
                                    $result[] = $link->getLinkText();
                                }
                            }
                        }

                        return implode(', ', $result);
                    },
                    'meVisible' => false,
                ]),
                'parent' => new Type\Integer([
                    'description' => t('Связанные задачи'),
                    'hint' => t('Отображает состояние связанных автозадач'),
                    'template' => '%crm%/form/task/parent.tpl',
                    'default' => 0
                ]),
                'title' => new Type\Varchar([
                    'description' => t('Суть задачи'),
                    'checker' => ['ChkEmpty', t('Опишите суть задачи в одном предложении')],
                    'meVisible' => false,
                ]),
                'status_id' => new Type\Integer([
                    'description' => t('Статус'),
                    'list' => [['\Crm\Model\Orm\Status', 'getStatusesTitles'], 'crm-task']
                ]),
                'type_id' => new Type\Integer([
                    'description' => t('Тип задачи'),
                    'hint' => t('Укажите тип задачи, если желаете запустить цепочку связанных автоматизаций'),
                    'list' => [['\Crm\Model\TaskTypeApi', 'staticSelectList'], [0 => t('- Не выбрано -')]]
                ]),
                'description' => new Type\Text([
                    'description' => t('Описание'),
                    'meVisible' => false,
                ]),
                'date_of_create' => new Type\Datetime([
                    'description' => t('Дата создания'),
                    'meVisible' => false,
                ]),
                'date_of_planned_end' => new Type\Datetime([
                    'description' => t('Планируемая дата завершения задачи')
                ]),
                'date_of_end' => new Type\Datetime([
                    'description' => t('Фактическая дата завершения задачи')
                ]),
                'date_of_update' => new Type\Datetime([
                    'description' => t('Дата обновления задачи'),
                    'visible' => false,
                ]),
                'creator_user_id' => new Type\User([
                    'description' => t('Создатель задачи'),
                    'compare' => function($value, $property, $orm) {
                        $user = new User($value);
                        return $user->getFio()."($value)";
                    }
                ]),
                'implementer_user_id' => new Type\User([
                    'description' => t('Исполнитель задачи'),
                    'requestUrl' => $router->getAdminUrl('ajaxEmail', [
                        'groups' => $config->implementer_user_groups
                    ], 'users-ajaxlist'),
                    'compare' => function($value, $property, $orm) {
                        $user = new User($value);
                        return $user->getFio()."($value)";
                    }
                ]),
                'collaborator_users_id' => new Type\Users([
                    'description' => t('Соисполнители'),
                    'requestUrl' => $router->getAdminUrl('ajaxEmail', [
                        'groups' => $config->implementer_user_groups
                    ], 'users-ajaxlist'),
                    'hint' => t('Соисполнители будут иметь такой же доступ к задаче, как и исполнитель'),
                ]),
                'observer_users_id' => new Type\Users([
                    'description' => t('Наблюдатели'),
                    'requestUrl' => $router->getAdminUrl('ajaxEmail', [
                        'groups' => $config->observer_user_groups
                    ], 'users-ajaxlist'),
                    'hint' => t('Наблюдатели будут получать уведомления об изменениях в задаче'),
                ]),
                'board_sortn' => new Type\Integer([
                    'description' => t('Сортировочный индекс на доске'),
                    'visible' => false
                ]),
            t('Чек-листы'),
                '__checklist__' => new Type\UserTemplate('%crm%/form/checklist/checklist.tpl'),
                'checklist' => new Type\ArrayList([
                    'description' => t('Чек-листы'),
                    'visible' => false,
                    'compare' => function($value, $property, $orm) {
                        $normalize = function (&$arr) use (&$normalize) {
                            if (!is_array($arr)) return;
                            foreach ($arr as &$v) {
                                if (is_array($v)) {
                                    $normalize($v);
                                } else {
                                    if ($v === "0" || $v === 0) {
                                        $v = "";
                                    }
                                }
                            }
                            ksort($arr);
                        };

                        if (!is_array($value)) {
                            return $value;
                        }

                        $normalize($value);

                        return $value;
                    }
                ])
        ]);

        if (!Rights::CheckRightError('crm', ModuleRights::TASK_CHAT_READ)) {
            $this->getPropertyIterator()->append([
                t('Чат'),
                    '__chat__' => new Type\UserTemplate('%crm%/form/chat/chat.tpl'),
                    'chat' => new Type\ArrayList([
                        'description' => t('Чат'),
                        'visible' => false
                    ]),
            ]);
        }

        /* Настройки автозадач будут удалены в следующих версиях */
        $this->getPropertyIterator()->append([
            //t('Настройки автозадачи'),
                'autotask_index' => new Type\Integer([
                    'description' => t('Порядковый номер автозадачи'),
                    'readOnly' => true,
                    'compare' => false,
                    'visible' => false,
                ]),
                'autotask_group' => new Type\Integer([
                    'description' => t('Идентификатор группы связанных заказов'),
                    'hint' => t('Используется в сгенерированных автоматически задачах'),
                    'readOnly' => true,
                    'compare' => false,
                    'visible' => false,
                ]),
                'is_autochange_status' => new Type\Integer([
                    'description' => t('Включить автосмену статуса'),
                    'checkboxView' => [1,0],
                    'compare' => false,
                    'visible' => false,
                ]),
                'autochange_status_rule_arr' => new Type\ArrayList([
                    'description' => t('Условия для смены статуса'),
                    'template' => '%crm%/form/tasktemplate/autochange_rule.tpl',
                    'checker' => [function($_this, $value) {
                        if ($_this['is_autochange_status'] && !$value) {
                            return t('Необходимо добавить хотя бы одно условие для смены статуса');
                        }
                        return true;
                    }],
                    'compare' => false,
                    'visible' => false,
                ]),
                'autochange_status_rule' => new Type\Text([
                    'description' => t('Условия для смены статуса'),
                    'visible' => false,
                    'compare' => false
                ]),
        ]);


        $user_field_manager = $config
            ->getTaskUserFieldsManager()
            ->setArrayWrapper('custom_fields');

        if ($user_field_manager->notEmpty()) {
            $this->getPropertyIterator()->append([
                t('Доп. поля'),
                'custom_fields' => new \Crm\Model\OrmType\CustomFields([
                    'description' => t('Доп.поля'),
                    'fieldsManager' => $user_field_manager,
                    'checker' => [['\Crm\Model\Orm\CustomData', 'validateCustomFields'], 'custom_fields'],
                    'compare' => function($value, $property, $orm) use($user_field_manager) {
                        $lines = [];
                        $user_field_manager->setValues($value);
                        foreach($user_field_manager->getStructure() as $item) {
                            $lines[] = $item['title'].':'.$item['current_val'];
                        }

                        return implode("<br>\n", $lines);
                    }
                ])
            ]);
        }

        $this->getPropertyIterator()->append([
            t('Файлы'),
                '__files__' => new \Files\Model\OrmType\Files([
                    'linkType' => self::FILES_LINK_TYPE
                ])
        ]);

        $this->getPropertyIterator()->append([
            t('Разное'),
            'expiration_notice_time' => new Type\Integer([
                'description' => t('Уведомить исполнителя о скором истечении срока выполнении задачи за...'),
                'hint' => t('Уведомление будет отправлено только, если статус задачи будет удовлетворять условиям в настройках модуля CRM и на сайте настроен планировщик'),
                'list' => [[$config, 'getNoticeExpirationTimeList']],
                'default' => $config->expiration_task_default_notice_time
            ]),
            'expiration_notice_date' => new Type\Datetime([
                'description' => t('Дата отправки уведомления об истечении срока выполнения')
            ]),
            'is_archived' => new Type\Integer([
                'allowEmpty' => false,
                'description' => t('Задача архивная?'),
                'hint' => t('Архивные задачи не отображаются на Kanban доске'),
                'checkboxView' => [1,0]
            ]),
            'autotask_identificator' => new Type\Varchar([
                'description' => t('Идентификатор автозадачи'),
                'readOnly' => true,
            ]),
            'autotask_root_id' => new Type\Varchar([
                'description' => t('Идентификатор корневой автозадачи'),
                'visible' => false,
            ]),
        ]);


        //Включаем в форму hidden поле id.
        $this['__id']->setVisible(true);
        $this['__id']->setMeVisible(false);
        $this['__id']->setHidden(true);

        $this->addIndex(['date_of_planned_end', 'status_id'], self::INDEX_KEY);
    }



    /**
     * Устанавливает права для полей ORM объекта
     *
     * @param string $flag Флаг опреации insert или update
     * @return void
     */
    public function initUserRights($flag)
    {
        $current_user_id = Auth::getCurrentUser()->id;

        if (!Rights::hasRight($this, ModuleRights::TASK_CHANGE_CREATOR_USER)) {
            $this['__creator_user_id']->setReadOnly(true);
            $this['__creator_user_id']->setListenPost(false);
            $this['__creator_user_id']->setMeVisible(false);
        }

        if ($flag == self::UPDATE_FLAG) {
            if (!Rights::hasRight($this, ModuleRights::TASK_CHANGE_IMPLEMENTER_USER)) {
                $this['__implementer_user_id']->setReadOnly(true);
                $this['__implementer_user_id']->setListenPost(false);
                $this['__implementer_user_id']->setMeVisible(false);

                $this['__collaborator_users_id']->setReadOnly(true);
                $this['__collaborator_users_id']->setListenPost(false);
                $this['__collaborator_users_id']->setMeVisible(false);

                $this['__observer_users_id']->setReadOnly(true);
                $this['__observer_users_id']->setListenPost(false);
                $this['__observer_users_id']->setMeVisible(false);
            }

            if (!Rights::hasRight($this, ModuleRights::TASK_CHANGE_PLANNED_END)) {
                $this['__date_of_planned_end']->setReadOnly(true);
                $this['__date_of_planned_end']->setListenPost(false);
                $this['__date_of_planned_end']->setMeVisible(false);
            }
        }
    }

    /**
     * Возвращает объект пользователя, создателя задачи
     *
     * @return User
     */
    public function getCreatorUser()
    {
        return new User($this['creator_user_id']);
    }

    /**
     * Возвращает объект пользователя, исполнителя задачи
     *
     * @return User
     */
    public function getImplementerUser()
    {
        return new User($this['implementer_user_id']);
    }

    /**
     * Возвращает список объектов пользователей-соисполнителей
     *
     * @return User[]
     */
    public function getCollaboratorUsers()
    {
        $users = [];
        foreach($this['collaborator_users_id'] as $user_id) {
            $users[] = new User($user_id);
        }
        return $users;
    }

    /**
     * Возвращает список объектов пользователей-наблюдателей
     *
     * @return User[]
     */
    public function getObserverUsers()
    {
        $users = [];
        foreach($this['observer_users_id'] as $user_id) {
            $users[] = new User($user_id);
        }
        return $users;
    }

    /**
     * Возвращает общий список пользователей по необходимым ролям
     *
     * @param array $roles роли
     * @param array $only_id возвращать только ID пользователей
     * @param array $add_creator добавить создателя в список
     * @return array
     */
    public function getUsersByRoles($roles, $only_id = true, $add_creator = false)
    {
        $users = [];
        foreach($roles as $role) {
            switch($role) {
                case UserLink::USER_ROLE_IMPLEMENTER:
                    if ($this['implementer_user_id']) {
                        $users[$this['implementer_user_id']] = $this['implementer_user_id'];
                    }
                    break;
                case UserLink::USER_ROLE_COLLABORATOR:
                    foreach($this['collaborator_users_id'] ?? [] as $user_id) {
                        if ($user_id) {
                            $users[$user_id] = $user_id;
                        }
                    }
                    break;
                case UserLink::USER_ROLE_OBSERVER:
                    foreach($this['observer_users_id'] ?? [] as $user_id) {
                        if ($user_id) {
                            $users[$user_id] = $user_id;
                        }
                    }
                    break;
            }
        }

        if ($add_creator) {
            if ($this['creator_user_id']) {
                $users[$this['creator_user_id']] = $this['creator_user_id'];
            }
        }else {
            if (isset($users[$this['creator_user_id']])) {
                unset($users[$this['creator_user_id']]);
            }
        }

        if (!$only_id) {
            $users = array_map(function($value) {
                return new User($value);
            }, $users);
        }

        return $users;
    }

    /**
     * Обработчик, вызывается перед сохранением объекта
     *
     * @param string $flag
     */
    public function beforeWrite($flag)
    {
        $config = Loader::byModule($this);
        $this->before = new self($this['id']);

        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }

        if (!$this['is_autochange_status']) {
            $this['autochange_status_rule_arr'] = [];
        }

        $this['autochange_status_rule'] = serialize($this['autochange_status_rule_arr']);

        $this['date_of_update'] = date('Y-m-d H:i:s');

        if ($flag == self::UPDATE_FLAG) {
            $statusId = $this->getStatus()->id;

            $isComplete = in_array($statusId, $config['complete_task_statuses']);
            $isCancelled = in_array($statusId, $config['cancel_task_statuses']);
            $isExpirationNotice = in_array($statusId, $config['expiration_task_notice_statuses']);

            if ($config['allow_archive_for_complete_statuses'] && $this['is_archived'] && !$isComplete && !$isCancelled) {
                return $this->addError(
                    t('Архивация доступна только для отмененных или выполненных задач. Невозможно архивировать задачу в статусе "%status".',
                    ['status' => $this->getStatus()->title])
                );
            }

            $no_right_change_creator = !Rights::hasRight($this, ModuleRights::TASK_CHANGE_CREATOR_USER);
            $no_right_change_implementer = !Rights::hasRight($this, ModuleRights::TASK_CHANGE_IMPLEMENTER_USER);

            if ($no_right_change_creator && $this->before['creator_user_id'] != $this['creator_user_id']) {
                return $this->addError($no_right_change_creator);
            }

            if ($no_right_change_implementer && $this->before['implementer_user_id'] != $this['implementer_user_id']) {
                return $this->addError($no_right_change_implementer);
            }

            if ($isComplete && !$this['date_of_end']) {
                $this['date_of_end'] = date('Y-m-d H:i:s');
            }
        }

        if ($flag == self::INSERT_FLAG) {
            //Устанавливаем максимальный сортировочный индекс
            $this['board_sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(board_sortn) as max')
                    ->from($this)
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Обработчик сохранения объекта
     *
     * @param string $flag
     */
    public function afterWrite($flag)
    {
        if ($this['_tmpid'] < 0) {
            //Переносим файлы к сохраненному объекту
            FileApi::changeLinkId($this['_tmpid'], $this['id'], self::FILES_LINK_TYPE);

        }

        if ($this->isModified('links')) {
            $this->before->fillLinks(); //Загрузим данные по предыдущему состоянию, чтобы далее сравнить изменения
            LinkManager::saveLinks($this->getLinkSourceType(), $this['id'], $this['links']);
        }

        //Сохраняем соисполнителей
        if ($this->isModified('collaborator_users_id')) {
            UserLink::saveUserLinks($this->getLinkSourceType(),
                $this['id'],
                $this['collaborator_users_id'],
                UserLink::USER_ROLE_COLLABORATOR);
        }

        if ($this->isModified('observer_users_id')) {
            UserLink::saveUserLinks($this->getLinkSourceType(),
                $this['id'],
                $this['observer_users_id'],
                UserLink::USER_ROLE_OBSERVER);
        }

        CustomData::saveCustomFields($this->getShortAlias(), $this['id'], $this['custom_fields']);

        if (!$this['disable_autochange_status'] && $this->before['status_id'] != $this['status_id']) {
            AutoTaskRuleApi::autoChangeStatus($this);
        }

        if ($flag == self::INSERT_FLAG && $this['implementer_user_id']) {
            $notice = new NewTaskToImplementer();
            $notice->init($this);
            Manager::send($notice);
        }

        if ($flag == self::UPDATE_FLAG) {
            if ($changes = $this->getChanges($this->before)) {
                ChatHistoryApi::writeSystemMessage($this, $flag, $changes);

                $current_user_id = Auth::getCurrentUser()->id;

                //Отправим уведомление об изменении объекта
                $notice = new ChangeTaskToUser();
                $notice->init($this, $changes, $current_user_id);

                Manager::send($notice);
            }
        }

        if ($flag == self::INSERT_FLAG) {
            ChatHistoryApi::writeSystemMessage($this, $flag);

            CallHistory::autoUpdateProcessedFlag();
        }

        $this->updateChecklistData();

        BoardApi::moveToFirst(new TaskApi(), $this);

        ViewManager::obj()
            ->setEntity($this)
            ->markAsViewed();
    }


    /**
     * Обработчик, вызывается сразу после загрузки объекта
     */
    public function afterObjectLoad()
    {
        //Сохраняем значения доп. полей в дополнительную таблицу
        $this['custom_fields'] = CustomData::loadCustomFields($this->getShortAlias(), $this['id']);
        $this['autochange_status_rule_arr'] = @unserialize((string)$this['autochange_status_rule']) ?: [];
        $this['collaborator_users_id'] = UserLink::getUserLinks($this->getLinkSourceType(),
            $this['id'],
            UserLink::USER_ROLE_COLLABORATOR);
        $this['observer_users_id'] = UserLink::getUserLinks($this->getLinkSourceType(),
            $this['id'],
            UserLink::USER_ROLE_OBSERVER);

        $this['checklist'] = CheckListGroupApi::getFullChecklistByTaskId($this['id']);

        $this['chat'] = ChatHistoryApi::getChatHistoryByTask($this);
    }

    /**
     * Возвращает идентификатор в менеджере связей
     *
     * @return string
     */
    public static function getLinkSourceType()
    {
        return 'task';
    }


    /**
     * Возвращает список возможных родительских объектов
     *
     * @return string[]
     */
    public static function getAllowedLinkTypes()
    {
        $allow_link_types = [
            LinkTypeDeal::getId(),
            LinkTypeCall::getId(),
            LinkTypeUser::getId()
        ];

        $event_result = EventManager::fire('crm.task.getlinktypes', $allow_link_types);
        $allow_link_types = $event_result->getResult();

        return $allow_link_types;
    }

    /**
     * Удаляет текущий объект, а также все ссылки на него
     *
     * @return bool
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            //Удаляем ссылки связи с объектами
            LinkManager::removeLinks($this->getLinkSourceType(), $this['id']);
        }
        return $result;
    }

    /**
     * Возвращает объект статуса
     *
     * @return mixed
     */
    public function getStatus()
    {
        return new Status($this['status_id']);
    }

    /**
     * Возвращает цвет, которым следует подсветить дату планируемого завершения
     * черным - если до нее более двух дней
     * желтым - если до нее менее двух дней
     * красным - если дата просрочена и дата фактического завершения задачи позже
     * зеленым - если дата просрочена, но дата фактического завершения уложилась в срок
     *
     * @return string
     */
    public function getPlannedEndStatus()
    {
        $planned_end_time = strtotime($this['date_of_planned_end']);
        $yellow_time = $planned_end_time - 60*60*24*self::ORANGE_STATUS_DAYS; //За 2 дня задачи будут оранжевым подсвечены

        $end_time = strtotime((string)$this['date_of_end']);
        $now = time();

        if ($now < $yellow_time) {

            return self::PLANNED_END_STATUS_BLACK;

        } elseif ($now >= $yellow_time && $now < $planned_end_time) {

            return self::PLANNED_END_STATUS_ORANGE;

        } else {
            if ($this['date_of_end'] && $end_time <= $planned_end_time) {
                return self::PLANNED_END_STATUS_GREEN;
            }

            return self::PLANNED_END_STATUS_RED;
        }
    }

    /**
     * Возвращает пояснение к планируемой дате завершения задачи
     *
     * @return string
     */
    public function getPlannedEndStatusTitle()
    {
        $status = $this->getPlannedEndStatus();

        switch ($status) {
            case self::PLANNED_END_STATUS_BLACK:
                $status_title = t('Срок еще не истек'); break;
            case self::PLANNED_END_STATUS_ORANGE:
                $status_title = t('Скоро истекает'); break;
            case self::PLANNED_END_STATUS_GREEN:
                $status_title = t('Задача завершена в срок'); break;
            case self::PLANNED_END_STATUS_RED:
                $status_title = t('Задача просрочена'); break;
        }

        return $status_title;
    }

    /**
     * Скрывает вкладку "Настройки автозадачи"
     */
    public function hideAutoTaskTab()
    {
        $this['__autotask_index']->setVisible(false);
        $this['__autotask_group']->setVisible(false);
        $this['__is_autochange_status']->setVisible(false);
        $this['__autochange_status_rule_arr']->setVisible(false);
    }

    /**
     * Возвращает массив с измененными полями в сравнении с предыдущим состоянием объекта
     *
     * @param Task $before_task
     * @return array
     */
    public function getChanges(Task $before_task)
    {
        $result = [];
        foreach($this->getProperties() as $key => $property) {
            if (isset($property->compare) && $property->compare === false) continue;

            if (isset($property->compare) && is_callable($property->compare)) {
                //Получаем значения из кастомной функции
                $current = call_user_func($property->compare, $this[$key], $property, $this);
                $before = call_user_func($property->compare, $before_task[$key], $property, $before_task);
            } else {
                //Получаем обычные значения
                $current = $property->textView();
                $before = $before_task['__'.$key]->textView();
            }

            if ($current != $before) {
                $result[$key] = [
                    'title' => $property->getDescription(),
                    'before_value' => $before,
                    'current_value' => $current
                ];
            }
        }

        return $result;
    }

    /**
     * Загружает данные в поле Links
     */
    public function fillLinks()
    {
        $links = \RS\Orm\Request::make()
            ->from(new \Crm\Model\Orm\Link())
            ->where([
                'source_type' => $this->getLinkSourceType(),
                'source_id' => $this['id']
            ])
            ->whereIn('link_type', $this['__links']->getAllowedLinkTypes())
            ->exec()->fetchSelected('link_type', 'link_id', true);

        $this['links'] = $links;
    }

    /**
     * Возвращает true, если задача создана текущим пользователем или назначена ему
     *
     * @return bool
     */
    public function isMyTask($user_id = null)
    {
        if ($user_id === null) {
            $user_id = Auth::getCurrentUser()->id;
        }

        return $this['creator_user_id'] == $user_id
                    || $this['implementer_user_id'] == $user_id
                    || in_array($user_id, $this['collaborator_users_id'] ?? []);
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return ModuleRights::TASK_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        if ($this->isMyTask()) {
            return ModuleRights::TASK_CREATE;
        } else {
            return ModuleRights::TASK_OTHER_CREATE;
        }
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        if ($this->isMyTask()) {
            return ModuleRights::TASK_UPDATE;
        } else {
            return ModuleRights::TASK_OTHER_UPDATE;
        }
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        if (!$this['id'] || $this->isMyTask()) {
            return ModuleRights::TASK_DELETE;
        } else {
            return ModuleRights::TASK_OTHER_DELETE;
        }
    }

    /**
     * Возвращает true, если пришло время уведомить об окончании
     * Учитывается исключительно фактор времени
     *
     * @return bool
     */
    public function isTimeToExpire()
    {
        $date_of_planned_end_tm = strtotime($this['date_of_planned_end']);
        $alert_time = $this['expiration_notice_time'];

        if ($alert_time > 0) {
            if (time() >= $date_of_planned_end_tm - $alert_time && time() < $date_of_planned_end_tm) {
                if (empty($this['expiration_notice_date']) ||
                    strtotime($this['expiration_notice_date']) + $alert_time <= $date_of_planned_end_tm) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Отправляет уведомление о скором истечении срока выполнения
     */
    public function sendExpireNotice()
    {
        $notice = new TaskSoonExpireToImplementer();
        $notice->init($this);

        Manager::send($notice);

        $this['expiration_notice_date'] = date('Y-m-d H:i:s');
        $this->update();
    }

    /**
     * Возвращает все связанные объекты задач
     *
     * @return array
     */
    public function getRelatedTasks()
    {
        if ($this['id']) {
            $q = Request::make()
                ->from(new Task())
                ->orderby('date_of_create');
            if ($this['autotask_root_id']) {
                $q->where(['id' => $this['autotask_root_id'], 'autotask_root_id' => $this['autotask_root_id']], null, 'AND',  'OR');
            }else {
                $q->where(['id' => $this['id'], 'autotask_root_id' => $this['id']], null, 'OR', 'OR');
            }
            return $q->objects();
        }
        return [];

    }

    /**
     * Возвращает список файлов, прикрепленных к задаче
     *
     * @return array
     */
    public function getFiles()
    {
        $file_api = new \Files\Model\FileApi();
        $file_api->setFilter([
            'link_type_class' => self::FILES_LINK_TYPE,
            'link_id' => $this['id']
        ]);
        return $file_api->getList();
    }

    /**
     * Возвращает текущий объект до внесения изменений
     *
     * @return Task
     */
    public function getBeforeObject()
    {
        return $this->before;
    }

    /**
     * Возвращает массив с иконками ролей пользователя в данной задаче.
     *
     * @return array
     */
    function getRoleIcons()
    {
        $roles = [];
        $user_id = Auth::getCurrentUser()->id;
        if ($this['creator_user_id'] == $user_id) {
            $roles[] = [
                'title' => t('Создатель'),
                'class' => 'zmdi zmdi-hc-lg zmdi-case-check',
                'color' => '#eb7c39',
            ];
        }
        if ($this['implementer_user_id'] == $user_id) {
            $roles[] = [
                'title' => t('Исполнитель'),
                'class' => 'zmdi zmdi-hc-lg zmdi-account',
                'color' => '#4c4ccf',
            ];
        }
        if (in_array($user_id, $this['collaborator_users_id'])) {
            $roles[] = [
                'title' => t('Соисполнитель'),
                'class' => 'zmdi zmdi-hc-lg zmdi-account-add',
                'color' => '#3b913b',
            ];
        }
        if (in_array($user_id, $this['observer_users_id'])) {
            $roles[] = [
                'title' => t('Наблюдатель'),
                'class' => 'zmdi zmdi-hc-lg zmdi-face',
                'color' => '#a7a7a7',
            ];
        }
        return $roles;
    }

    /**
     * Проверяет, является ли задача новой (непрочитанной) для текущего пользователя
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return ViewManager::obj()->setEntity($this)->isNew();
    }

    /**
     * Возвращает количество непрочитанных сообщений в чате
     *
     * @return int
     */
    public function getUnreadChatMessagesCount()
    {
        return ViewManager::obj()->setEntity($this)->getUnreadChatMessagesCount();
    }

    /**
     * Возвращает иконку статуса родительской задачи.
     *
     * @return array
     */
    function getParentStatusIcon()
    {
        $icon = null;

        if ($this->autotask_identificator && $this->autotask_root_id) {
            $root_task = new self($this->autotask_root_id);
            if ($root_task->id) {
                $config = Loader::byModule($this);
                $status = $root_task->getStatus();
                $icon = [
                    'title' => t("Основная задача в статусе '%status_title'", ['status_title' => $status->title]),
                    'class' => 'zmdi zmdi-hc-lg zmdi-info',
                    'color' => '#a7a7a7',
                ];
                if (in_array($status->id, $config['complete_task_statuses'])) {
                    $icon['color'] = '#6ad183';
                    $icon['class'] = 'zmdi zmdi-hc-lg zmdi-check-circle';
                }

                if (in_array($status->id, $config['cancel_task_statuses'])) {
                    $icon['color'] = '#fb5a5a';
                    $icon['class'] = 'zmdi zmdi-hc-lg zmdi-close-circle';
                }
            }
        }

        return $icon;
    }

    /**
     * Проверяет право на изменение чеклиста
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function canChecklistUpdate()
    {
        return !Rights::CheckRightError('crm', ModuleRights::TASK_OTHER_CHECKLIST_UPDATE);
    }

    /**
     * Проверяет право на добавление сообщения в чате
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function canChatUpdate()
    {
        return !Rights::CheckRightError('crm', ModuleRights::TASK_CHAT_UPDATE);
    }

    /**
     * Проверяет право на просмотр файлов в чате
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function canChatReadFiles()
    {
        return !Rights::CheckRightError('crm', ModuleRights::TASK_CHAT_READ_FILES);
    }

    /**
     * Проверяет право на добавление файлов в чате
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function canChatAddFiles()
    {
        return !Rights::CheckRightError('crm', ModuleRights::TASK_CHAT_ADD_FILES);
    }

    /**
     * Возвращает прогресс чек-листа в формате:
     * ['done' => X, 'total' => Y, 'percent' => Z]
     *
     * @return false|array
     */
    public function getChecklistProgress()
    {
        $groups = CheckListGroupApi::getFullChecklistByTaskId($this['id']);

        if ($groups) {
            $total = 0;
            $done = 0;

            foreach ($groups as $group) {
                foreach ($group['items'] as $item) {
                    $total++;
                    if (!empty($item['is_done'])) {
                        $done++;
                    }
                }
            }

            $percent = ($total > 0) ? round(($done / $total) * 100) : 0;

            return ['done' => $done, 'total' => $total, 'percent' => $percent];
        }

        return false;
    }

    /**
     * Возвращает массивы существующих групп и чек-листов у задачи
     *
     * @return array[]
     */
    private function collectExistData()
    {
        $groups = CheckListGroupApi::getFullChecklistByTaskId($this['id']);

        $existing_group_uniqs = [];
        $existing_item_uniqs = [];

        foreach ($groups as $group_uniq => $group) {
            $existing_group_uniqs[] = $group_uniq;

            foreach ($group['items'] as $item_uniq => $item) {
                $existing_item_uniqs[] = $item_uniq;
            }
        }

        return [$existing_group_uniqs, $existing_item_uniqs];
    }

    /**
     * Удаляет группы и чек-листы из БД
     *
     * @param $item_uniqs - массив идентификаторов удаленных чек-листов
     * @param $group_uniqs - массив идентификаторов удаленных групп
     * @return void
     */
    private function deleteNonExistData($item_uniqs, $group_uniqs)
    {
        if ($item_uniqs) {
            Request::make()
                ->delete()
                ->from(new ChecklistItem())
                ->whereIn('uniq', $item_uniqs)
                ->exec();
        }

        if ($group_uniqs) {
            Request::make()
                ->delete()
                ->from(new ChecklistGroup())
                ->whereIn('uniq', $group_uniqs)
                ->exec();
        }
    }

    /**
     * Обновляет данные чек-листа задачи
     *
     * @return void
     */
    protected function updateChecklistData()
    {
        if ($this['checklist']) {
            list($deleted_group_uniqs, $deleted_item_uniqs) = $this->collectExistData();
            $can_edit = $this->canChecklistUpdate();

            foreach ($this['checklist'] as $group_uniq => $group_data) {
                $group = ChecklistGroup::loadByUniq($group_uniq);

                if (!$can_edit && !$group) {
                    continue;
                }

                $group->uniq = $group_uniq;
                $group->task_id = $this->id;

                if ($can_edit) {
                    $group->title = $group_data['title'];
                    $group->insert(false, ['title']);
                }


                if (!empty($group_data['items']) && is_array($group_data['items'])) {
                    $sortn = 0;
                    foreach ($group_data['items'] as $item_uniq => $item_data) {
                        $item = ChecklistItem::loadByUniq($item_uniq);

                        if (!$can_edit && !$item) {
                            continue;
                        }

                        $item->group_id = $group->id;
                        $item->uniq = $item_uniq;

                        if ($can_edit) {
                            $item->title = $item_data['title'];
                            $item->sortn = $sortn++;
                            $item->entity_type = $item_data['entity_type'] ?? '';
                            $item->entity_id = $item_data['entity_id'] ?? '';
                        }

                        $item->is_done = $item_data['is_done'];

                        $fields = ['is_done'];
                        if ($can_edit) {
                            $fields = ['title', 'sortn', 'entity_type', 'entity_id', 'is_done'];
                        }

                        $item->insert(false, $fields);

                        if (in_array($item_uniq, $deleted_item_uniqs)) {
                            unset($deleted_item_uniqs[array_search($item_uniq, $deleted_item_uniqs)]);
                        }
                    }
                }

                if (in_array($group_uniq, $deleted_group_uniqs)) {
                    unset($deleted_group_uniqs[array_search($group_uniq, $deleted_group_uniqs)]);
                }
            }

            if ($can_edit) {
                $this->deleteNonExistData($deleted_item_uniqs, $deleted_group_uniqs);
            }
        }
    }

}
