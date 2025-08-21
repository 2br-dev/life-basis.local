<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use RS\Http\Request;
use Support\Model\Orm\Support;

/**
* Уведомление - обращение от службы поддержки
*/
class User extends AbstractNotice
    implements InterfaceEmail, InterfaceSms
{
    public
        $user,  // объект пользователя, которому будет отправлено сообщение
        $support;
        

    public function getDescription()
    {
        return t('Сообщение от службы поддержки (Пользователю)');
    } 

    function init(Support $support)
    {
        $this->support = $support;
    }
    
    /**
    * Установка пользователя, которому будет отправлено сообщение
    * 
    * @param \Users\Model\Orm\User $user - объект пользователя
    */
    function setUser(\Users\Model\Orm\User $user){
       $this->user = $user; 
    }
    
    function getNoticeDataEmail()
    {
        $notice_data = new NoticeDataEmail();
        
        $notice_data->email     = $this->user['e_mail'];
        $notice_data->subject   = t('Ответ службы поддержки '). Request::commonInstance()->getDomainStr();
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%support%/notice/fromadmin_support.tpl';
    }
    
    
    function getNoticeDataSms()
    {
        $notice_data = new NoticeDataSms();
        
        if(!$this->user['phone']) return;
        
        $notice_data->phone     = $this->user['phone'];
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%support%/notice/fromadmin_support_sms.tpl';
    }
    
}
