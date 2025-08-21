<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Crm\Model\View;

use Crm\Model\Orm\ChatHistory;
use Crm\Model\Orm\ViewMarker;
use RS\Application\Auth;
use RS\Orm\Request;

/**
 * Класс содержит методы для работы с персональной подсветкой изменений объектов взаимодействия в CRM
 */
class Manager
{
    const VIEW_TYPE_TASK = 'task';

    private static $instance;

    private $entity;

    /**
     * Возвращает объект текущего класса
     *
     * @return Manager
     */
    public static function obj()
    {
        if ( !isset(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Устанавливает объект, с которым будем работать
     *
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Помечает объект как просмотренный
     *
     * @return bool
     */
    public function markAsViewed()
    {
        if (!$this->entity) {
            throw new \Exception("Объект не установлен. Используйте setEntity().");
        }

        $user_id = Auth::getCurrentUser()->id;
        if (!$user_id) {
            return false;
        }

        $entityType = trim(str_replace('crm-', '', $this->entity->getShortAlias()));
        $entityId = $this->entity['id'];
        $status = $this->entity['status_id'];

        $existing_marker = Request::make()
            ->from(new ViewMarker())
            ->where([
                'user_id' => $user_id,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'record_type' => 'view',
                'status' => $status
            ])
            ->object();

        if ($existing_marker) {
            $existing_marker['last_date'] = date('Y-m-d H:i:s');
            return $existing_marker->update();
        } else {
            $marker = new ViewMarker();
            $marker['user_id'] = $user_id;
            $marker['entity_type'] = $entityType;
            $marker['entity_id'] = $entityId;
            $marker['record_type'] = 'view';
            $marker['status'] = $status;
            $marker['last_date'] = date('Y-m-d H:i:s');

            return $marker->insert();
        }
    }

    /**
     * Проверяет, является ли установленный объект новым (непрочитанным)
     *
     * @return bool
     */
    public function isNew()
    {
        if (!$this->entity) {
            throw new \Exception("Объект не установлен в ViewManager");
        }

        $user_id = Auth::getCurrentUser()->id;
        if (!$user_id) return true;

        $entity_id = $this->entity['id'];
        $entity_type = self::VIEW_TYPE_TASK;
        $update_date = $this->entity['date_of_update'];
        $status = $this->entity['status_id'];

        $reset_date = Request::make()
            ->from(new ViewMarker())
            ->where([
                'user_id' => $user_id,
                'entity_type' => $entity_type,
                'record_type' => 'reset',
                'status' => $status
            ])
            ->exec()
            ->getOneField('last_date', null);

        if (!$update_date && $reset_date || ($update_date && $reset_date && $update_date < $reset_date)) return false;

        $view_date = Request::make()
            ->from(new ViewMarker())
            ->where([
                'user_id' => $user_id,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'record_type' => 'view',
                'status' => $status
            ])
            ->exec()
            ->getOneField('last_date', null);

        return !$view_date || $view_date && $view_date < $update_date;
    }

    /**
     * Помечает все объекты определенного типа и статуса как прочитанные
     *
     * @param $entity_type
     * @param $status_id
     * @return bool
     */
    public function markAllAsViewed($entity_type, $status_id = null)
    {
        $user_id = Auth::getCurrentUser()->id;
        if (!$user_id) return false;

        $now = date('Y-m-d H:i:s');

        $where = [
            'user_id' => $user_id,
            'entity_type' => $entity_type,
            'record_type' => 'view'
        ];
        if ($status_id) {
            $where['status'] = $status_id;
        }

        Request::make()
            ->delete()
            ->from(new ViewMarker())
            ->where($where)
            ->exec();

        $reset_where = [
            'user_id' => $user_id,
            'entity_type' => $entity_type,
            'record_type' => 'reset'
        ];
        if ($status_id) {
            $reset_where['status'] = $status_id;
        }
        if ($this->entity) {
            $reset_where['entity_id'] = $this->entity['autotask_root_id'] ?: $this->entity['id'];
        }

        $resetMarker = Request::make()
            ->from(new ViewMarker())
            ->where($reset_where)
            ->object();

        if ($resetMarker) {
            $resetMarker['last_date'] = $now;
            return $resetMarker->update();
        } else {

            $resetMarker = new ViewMarker();
            $resetMarker['user_id'] = $user_id;
            $resetMarker['entity_type'] = $entity_type;
            $resetMarker['record_type'] = 'reset';
            $resetMarker['status'] = $status_id;
            $resetMarker['last_date'] = $now;
            if ($this->entity) {
                $resetMarker['entity_id'] = $this->entity['autotask_root_id'] ?: $this->entity['id'];
            }

            return $resetMarker->insert();
        }
    }

    /**
     * Возвращает количество непрочитанных сообщений в чате для пользователя
     *
     * @return int
     */
    public function getUnreadChatMessagesCount()
    {
        $user_id = Auth::getCurrentUser()->id;
        if (!$user_id) return 0;

        $chat_history = new ChatHistory();
        $entity_type = trim(str_replace('crm-', '', $chat_history->getShortAlias()));

        $root_id = $this->entity['autotask_root_id'] ?: $this->entity['id'];

        $where_reset = [
            'user_id' => $user_id,
            'entity_type' => $entity_type,
            'record_type' => 'reset',
            'entity_id' => $root_id,
        ];

        $last_view_date = Request::make()
            ->from(new ViewMarker())
            ->where($where_reset)
            ->exec()
            ->getOneField('last_date');

        if (!$last_view_date) {
            $where_view = [
                'user_id' => $user_id,
                'entity_type' => $entity_type,
                'entity_id' => $root_id,
                'record_type' => 'view',
            ];

            $last_view_date = Request::make()
                ->from(new ViewMarker())
                ->where($where_view)
                ->exec()
                ->getOneField('last_date');
        }

        $query = Request::make()
            ->from($chat_history)
            ->where([
                'autotask_root_id' => $root_id,
                'type' => 'message',
            ])
            ->where('user_id != "#user_id"', ['user_id' => $user_id]);

        if ($last_view_date) {
            $query->where("date_of_create > '#date'", ['date' => $last_view_date]);
        }

        return $query->count();
    }
}
