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
use RS\Config\Loader;
use RS\Helper\Tools;
use RS\Http\Request;
use RS\Router\Manager;
use Support\Model\Orm\Support;

/**
 * Уведомление - новая тема(тикет) в поддержке для администратора
 */
class NewTopicToAdmin extends AbstractNotice
    implements InterfaceEmail,
    InterfaceSms,
    InterfaceDesktopApp
{
    public $topic;
    public $support;

    /**
     * Возвращает наименование данного уведомления
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Новая тема(тикет) в поддержке (администратору)');
    }

    /**
     * Инициализирует параметры текущего уведомления
     *
     * @param \Support\Model\Orm\Topic $topic
     */
    function init(Support $support)
    {
        $this->topic = $support->getTopic();
        $this->support = $support;
    }

    /**
     * Возвращает параметры текущего уведомления
     *
     * @return NoticeDataEmail
     */
    function getNoticeDataEmail()
    {
        $site_config = Loader::getSiteConfig();

        $notice_data = new NoticeDataEmail();

        $notice_data->email     = $site_config['admin_email'];
        $notice_data->subject   = t('Создан новый тикет №%number на сайте %domain', [
            'number' => $this->topic['id'],
            'domain' => Request::commonInstance()->getDomainStr()
        ]);
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
        return '%support%/notice/toadmin_topic.tpl';
    }

    /**
     * Возвращает параметры для уведомления по SMS
     *
     * @return NoticeDataSms|void
     */
    function getNoticeDataSms()
    {
        $site_config = Loader::getSiteConfig();

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
        return '%support%/notice/toadmin_topic_sms.tpl';
    }

    /**
     * Возвращает путь к шаблону уведомления для Desktop приложения
     *
     * @return string
     */
    public function getTemplateDesktopApp()
    {
        return '%support%/notice/desktop_topic.tpl';
    }

    /**
     * Возвращает данные, которые необходимо передать при инициализации уведомления
     *
     * @return NoticeDataDesktopApp
     */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new NoticeDataDesktopApp();
        $notice_data->title = t('Новый тикет №%number на сайте %domain', [
            'number' => $this->topic['id'],
            'domain' => Request::commonInstance()->getDomainStr()
        ]);

        $notice_data->short_message = t('%user %nl%title', [
            'nl' => "\n",
            'user' => $this->topic->getUser()->getFio(),
            'title' => Tools::teaser($this->topic['title'], 100, true)
        ]);

        $notice_data->link = Manager::obj()->getAdminUrl(false, ['id' => $this->topic['id']], 'support-supportctrl', true);
        $notice_data->link_title = t('Перейти к переписке');
        $notice_data->vars = $this;

        return $notice_data;
    }
}
