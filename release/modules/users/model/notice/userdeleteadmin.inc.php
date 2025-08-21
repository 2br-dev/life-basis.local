<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Model\Notice;

/**
* Уведомление - запрос на удаление профиля пользователя
*/
class UserDeleteAdmin extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms 
{
    public
        $user,
        $password;

    public function getDescription()
    {
        return t('Запрос на удаление пользователя (администратору)');
    } 

    
    function init(\Users\Model\Orm\User $user)
    {
        $this->user = $user;
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataEmail
     */
    function getNoticeDataEmail()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        $notice_data->email      = $config['admin_email'];
        $notice_data->subject    = t('Запрос на удаление профиля на сайте ').\RS\Http\Request::commonInstance()->getDomainStr();
        $notice_data->vars       = $this;
        
        return $notice_data;
    }

    /**
     * Возвращает объект NoticeData
     *
     * @return \Alerts\Model\Types\NoticeDataSms|void
     */
    function getNoticeDataSms()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        if(!$config['admin_phone']) return;
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        $notice_data->phone      = $config['admin_phone'];
        $notice_data->vars       = $this;
        
        return $notice_data;
    }

    /**
     * Возвращает шаблон для eMail
     *
     * @return string
     */
    function getTemplateEmail()
    {
        return '%users%/notice/toadmin_deleteprofile.tpl';
    }

    /**
     * Возвращает шаблон для СМС
     *
     * @return string
     */
    function getTemplateSms()
    {
        return '%users%/notice/toadmin_deleteprofile_sms.tpl';
    }
}

