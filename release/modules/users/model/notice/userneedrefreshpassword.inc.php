<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\NoticeDataEmail;
use RS\Http\Request;
use RS\Router\Manager;
use Users\Model\Orm\User;

/**
 * Уведомление - необходимость обновления пароля
 */
class UserNeedRefreshPassword extends AbstractNotice implements InterfaceEmail
{
    public $user;
    public $domain;
    public $change_url;
    public $confirm_url;

    public function getDescription()
    {
        return t('Уведомление о необходимости обновить пароль (пользователю)');
    }

    function init(User $user)
    {
        $this->user = $user;
        $this->domain = Request::commonInstance()->getDomainStr();

        $router = Manager::obj();
        $this->change_url = $router->getUrl('users-front-auth', ['Act' => 'changePassword', 'uniq' => $this->user['hash']], true);
        $this->confirm_url = $router->getUrl('users-front-auth', ['Act' => 'confirmPassword', 'uniq' => $this->user['hash']], true);
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return NoticeDataEmail|void
     */
    function getNoticeDataEmail()
    {
        $notice_data = new NoticeDataEmail();
        $notice_data->email       = $this->user['e_mail'];
        $notice_data->subject     = t('Необходимо обновить или подтвердить пароль на сайте ').$this->domain;
        $notice_data->vars        = $this;

        return $notice_data;
    }

    /**
     * Возвращает путь к шаблону письма
     *
     * @return string
     */
    function getTemplateEmail()
    {
        return '%users%/notice/touser_need_refresh_password.tpl';
    }
}