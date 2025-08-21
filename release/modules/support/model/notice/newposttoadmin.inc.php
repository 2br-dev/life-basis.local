<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Notice;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceDesktopApp;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use Alerts\Model\Types\NoticeDataDesktopApp;
use Alerts\Model\Types\NoticeDataEmail;
use Alerts\Model\Types\NoticeDataSms;
use RS\Helper\Tools;
use RS\Router\Manager;
use Support\Model\Orm\Support;

/**
* Уведомление - обращение в поддержку
*/
class NewPostToAdmin extends AbstractNotice
           implements InterfaceEmail,
                      InterfaceSms,
                      InterfaceDesktopApp
{
    public $support;

    /**
     * Возвращает наименование данного уведомления
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Сообщение в службу поддержки (администратору)');
    }

    /**
     * Инициализирует параметры текущего уведомления
     *
     * @param Support $support
     */
    function init(Support $support)
    {
        $this->support = $support;
    }

    /**
     * Возвращает параметры текущего уведомления
     *
     * @return NoticeDataEmail
     */
    function getNoticeDataEmail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new NoticeDataEmail();
        
        $notice_data->email     = $site_config['admin_email'];
        $notice_data->subject   = t('Сообщение в поддержку ').\RS\Http\Request::commonInstance()->getDomainStr();
        $notice_data->vars      = $this;
        
        return $notice_data;
    }

    /**
     * Возвращает шаблон уведомления
     *
     * @return string
     */
    function getTemplateEmail()
    {
        return '%support%/notice/toadmin_support.tpl';
    }

    /**
     * Возвращает параметры для уведомления по SMS
     *
     * @return NoticeDataSms|void
     */
    function getNoticeDataSms()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new NoticeDataSms();
        
        if(!$site_config['admin_phone']) return;
        
        $notice_data->phone     = $site_config['admin_phone'];
        $notice_data->vars      = $this;
        
        return $notice_data;
    }

    /**
     * Возвращает шаблон уведомления по SMS
     *
     * @return string
     */
    function getTemplateSms()
    {
        return '%support%/notice/toadmin_support_sms.tpl';
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string
    */
    public function getTemplateDesktopApp() 
    {
        return '%support%/notice/desktop_support.tpl';
    }
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NoticeDataDesktopApp
    */
    public function getNoticeDataDesktopApp() 
    {
        $notice_data = new NoticeDataDesktopApp();
        $notice_data->title = t('Обращение в поддержку на сайте');
        
        $notice_data->short_message = t('%user %nl%message', [
            'nl' => "\n",
            'user' => $this->support->getUser()->getFio(),
            'message' => Tools::teaser($this->support->message, 100, true)
        ]);
        
        $notice_data->link = Manager::obj()->getAdminUrl(false, ['id' => $this->support->topic_id], 'support-supportctrl', true);
        $notice_data->link_title = t('Перейти к переписке');
        $notice_data->vars = $this;
        
        return $notice_data;                
    }
}
