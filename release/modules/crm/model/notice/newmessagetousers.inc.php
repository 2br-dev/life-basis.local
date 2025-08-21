<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceDesktopApp;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\NoticeDataDesktopApp;
use Alerts\Model\Types\NoticeDataEmail;
use Crm\Model\Orm\ChatHistory;
use Crm\Model\Orm\Task;
use Crm\Model\Orm\UserLink;
use RS\Application\Auth;
use RS\Config\Loader;

/**
 * Уведомление о новом сообщении в чате
 */
class NewMessageToUsers extends AbstractNotice implements InterfaceEmail, InterfaceDesktopApp
{
    /**
     * @var Task
     */
    public $task;

    /**
     * @var ChatHistory
     */
    public $message;
    public $destination_users;
    public $current_user;

    /**
     * Возвращает краткое описание уведомления
     * @return string
     */
    public function getDescription()
    {
        return t('Новое сообщение в чате (исполнителям и наблюдателям)');
    }


    function init(Task $task, ChatHistory $message)
    {
        $this->task = $task;
        $this->message = $message;

        $destination_users = $this->task->getUsersByRoles([
            UserLink::USER_ROLE_IMPLEMENTER,
            UserLink::USER_ROLE_COLLABORATOR,
            UserLink::USER_ROLE_OBSERVER
        ], false, true);
        $current_user = Auth::getCurrentUser();

        unset($destination_users[$current_user['id']]);

        $this->destination_users = $destination_users;
        $this->current_user = $current_user;
    }


    /**
     * Возвращает путь к шаблону уведомления для Desktop приложения
     *
     * @return string
     */
    public function getTemplateDesktopApp()
    {
        return '%crm%/notice/new_message_to_users_desktop.tpl';
    }

    /**
     * Возвращает данные, которые необходимо передать при инициализации уведомления
     *
     * @return NoticeDataDesktopApp
     */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new NoticeDataDesktopApp();
        $notice_data->title = t('Новое сообщение в чате у задачи №%0', [$this->task->task_num]);

        if ($this->destination_users) {
            $notice_data->destination_user_id = array_keys($this->destination_users);
            $notice_data->short_message = t('%message %creator', [
                'message' => $this->message['message'],
                'creator' => ($this->current_user ? t('от %0', [$this->current_user->getFio()]) : '')
            ]);
            $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->task['id']], 'crm-taskctrl', true);
            $notice_data->link_title = t('Перейти к задаче');
            $notice_data->vars = $this;

            return $notice_data;
        }

        return null;
    }

    /**
     * Возвращает путь к шаблону письма
     * @return string
     */
    public function getTemplateEmail()
    {
        return '%crm%/notice/new_message_to_users_email.tpl';
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataEmail
     */
    public function getNoticeDataEmail()
    {
        $notice_data = new NoticeDataEmail();
        $emails = [];
        foreach($this->destination_users as $user) {
            $emails[] = $user->e_mail;
        }

        $notice_data->email     = implode(',', $emails);
        $notice_data->subject   = t('Новое сообщение в чате у задачи №%0', [$this->task['task_num']]);
        $notice_data->vars      = $this;

        return $notice_data;
    }
}