<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Helper\Paginator;
use RS\Orm\Type\Richtext;
use Support\Model\Api;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;
use Support\Model\TopicApi;

/**
 * Контроллер просмотра сообщений одного тикета
 */
class SupportCtrl extends Crud
{
    const QUICK_VIEW_PAGE_SIZE = 20;

    protected $topic_api;
    /**
     * @var $api Api
     */
    protected $api;

    function __construct()
    {
        $this->topic_api = new TopicApi();
        parent::__construct(new Api);
    }
    
    function helperIndex()
    {
        $topic_id = $this->url->request('id', TYPE_INTEGER);
        $topic = new Topic($topic_id);
        $this->api->setFilter('topic_id', $topic_id);

        $helper = new CrudCollection($this, $this->api, $this->url);
        $helper->viewAsAny();
        $helper->setTopTitle($topic->title);;
        $helper->setTopToolbar($this->buttons(['moduleconfig']));
        return $helper;
    }

    /**
     * Страница ведения переписки
     *
     * @return mixed|\RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    function actionIndex()
    {
        $topic_id = $this->url->request('id', TYPE_INTEGER);
        $topic = new Topic($topic_id);
        if (!$topic['id']) {
            $this->e404(t('Тема не найдена'));
        }

        $message = new Support();
        //Для административной панели подменяем поле на визуальный редактор
        $message->setMessageRichText($topic->getPlatform()->getTinyLinkType());

        if ($this->url->isPost()) {
            $user_data = [
                'topic_id' => $topic['id'],
                'message_type' => Support::TYPE_ADMIN_MESSAGE,
                'message_format' => Support::MESSAGE_FORMAT_HTML,
                'is_admin' => 1,
                'user_id' => $this->user->id
            ];
            if ($message->save(null, $user_data)) {
                $this->api->markViewedList($topic['id'], false);
                $this->app->redirect($this->url->getSelfUrl().'#answer');
            }
        }

        $this->view->assign([
            'topic' => $topic,
            'new_message' => $message,
            'cancel_url' => $this->url->getSavedUrl('Support\Controller\Admin\TopicsCtrl'.'index'),
            'managers' => $this->api->getManagers(),

            'is_enable_autoupdate' => $this->getModuleConfig()->enable_autoupdate_tickets,
            'mp3_folder_url' => \Setup::$MODULE_FOLDER.'/support/view/sound',
            'config' => $this->getModuleConfig()
        ]);

        return $this->result->setTemplate('admin/topic_view.tpl');
    }

    /**
     * Получение новых сообщений для текущего пользователя
     */
    function actionAjaxGetNewMessages()
    {
        $last_id = $this->url->get('last_message_id', TYPE_INTEGER);
        $topic_id = $this->url->get('topic_id', TYPE_INTEGER);

        $topic = new Topic($topic_id);
        if (!$topic['id']) {
            $this->e404(t('Тема не найдена'));
        }

        $messages = [];
        if ($last_id) {
            $this->api->setFilter('id', $last_id, '>');
            $this->api->setFilter('topic_id', $topic['id']);
            $this->api->setFilter('message_type', Support::TYPE_USER_SYSTEM_MESSAGE, '!=');
            $this->api->setOrder('id');
            $messages = $this->api->getList();
        }

        $this->view->assign([
            'messages' => $messages
        ]);

        return $this->result
                        ->setTemplate('%support%/admin/ticket_view_messages.tpl')
                        ->addSection('count', count($messages))
                        ->addSection('last_message_id', $topic->getLastMessageId());
    }

    /**
     * Быстрый переход к другим темам
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxQuickShowTopics()
    {
        $page = $this->url->request('p', TYPE_INTEGER, 1);
        $exclude_id = $this->url->request('exclude_id', TYPE_INTEGER);

        $request_object = $this->topic_api->getSavedRequest('Support\Controller\Admin\TopicsCtrl' . '_list');

        if ($request_object) {
            $this->topic_api->setQueryObj($request_object);
            $this->topic_api->setFilter("id", $exclude_id, '!=');
        }

        $paginator = new Paginator($page, $this->topic_api->getListCount(), self::QUICK_VIEW_PAGE_SIZE);

        $this->view->assign([
            'topics' => $this->topic_api->getList($page, self::QUICK_VIEW_PAGE_SIZE),
            'paginator' => $paginator
        ]);

        return $this->result
            ->addSection(['title' => t('Быстрый просмотр тем'),])
            ->setTemplate('admin/quick_show_topics.tpl');
    }

    /**
     * Форма добавления элемента
     *
     * @param mixed $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении, иначе будет вызов стандартного _successSave метода
     * @param CrudCollection $helper - текуй хелпер
     * @return \RS\Controller\Result\Standard|bool
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKeyValue) {
            $support = $this->api->getElement();
            if ($support['message_format'] == Support::MESSAGE_FORMAT_HTML) {
                $topic = $support->getTopic();
                $support->setMessageRichText($topic->getPlatform()->getTinyLinkType());
            }
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
}
