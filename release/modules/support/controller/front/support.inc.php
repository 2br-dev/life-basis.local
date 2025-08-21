<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Controller\Front;

use RS\Application\Application;
use RS\Controller\AuthorizedFront;
use RS\File\Tools;
use Support\Model\Api;
use Support\Model\Orm\Attachment;
use Support\Model\Orm\Support as OrmSupport;
use Support\Model\Platform\PlatformSite;
use Support\Model\TopicApi;

/**
 * Контроллер Поддержки в клиентской части
 */
class Support extends AuthorizedFront
{
    protected
        $api,
        $topic_api;


    function init()
    {
        $this->api = new Api();
        $this->topic_api = new TopicApi();
        $this->view->assign([
            'config' => $this->getModuleConfig()
        ]);
    }

    /**
     * Отображает список тем, по которым были обращения с сайта
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {        
        $this->app->breadcrumbs->addBreadCrumb(t('Поддержка'));
        $support_item = $this->api->getNewElement();            
                
        if ($this->url->isPost()) {
            $addpost = [
                'user_id' => $this->user['id'], 
                'is_admin' => 0,
                'message_type' => OrmSupport::TYPE_USER_MESSAGE,
                'topic_platform' => PlatformSite::PLATFORM_ID,
                'is_first_topic_message' => true
            ];
            if ($support_item->save(null, $addpost)) {
                //Тема создана, перходим в тему
                Application::getInstance()->redirect( $this->router->getUrl('support-front-support', ['Act' => 'viewTopic', 'id' => $support_item['topic_id']]) );
            }
        }

        $this->topic_api->setOrder('updated DESC');
        $this->topic_api->setFilter('user_id', $this->user['id']);
        $this->topic_api->setUserAccountPlatformFilter();
        $list = $this->topic_api->getList();            
        
        if (!$this->url->isPost() && count($list)>0) {
            $support_item['topic_id'] = $list[0]['id'];
        }
        
        $this->view->assign([
            'supp' => $support_item, 
            'list' => $list,
        ]);
        
        return $this->result->setTemplate( 'topics.tpl' );        
    }

    /**
     * Страница переписки в рамках одной темы
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionViewTopic()
    {
        $topic_id = $this->url->get('id', TYPE_INTEGER);
        if ($topic_id) {
            $topic = $this->topic_api->getOneItem($topic_id);
        } else {
            $number = $this->url->get('number', TYPE_STRING);
            $topic = $this->topic_api->getByNumber($number);
        }
        
        if (!$topic || $topic['user_id'] != $this->user['id']) {
            $this->e404(t('Такой темы не существует'));
        }

        if (!in_array($topic['platform'], $this->topic_api->getUserAccountPlatForms())) {
            $this->e404(t('Просмотр этой платформы на сайте недоступен'));
        }
        
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Поддержка'), $this->router->getUrl('support-front-support'))
            ->addBreadCrumb($topic['title']);
        
        $support_item = $this->api->getNewElement();
        $support_item->escapeAll(true);        
        
        if ($this->url->isPost()) {
            $addpost = [
                'user_id' => $this->user['id'], 
                'is_admin' => 0,
                'topic_id' => $topic['id'],
                'message_type' => OrmSupport::TYPE_USER_MESSAGE,
                'topic_platform' => PlatformSite::PLATFORM_ID
            ];
            if ($support_item->save(null, $addpost)) {
                //Сообщение сохранено
                Application::getInstance()->redirect($this->url->selfUri());
            }
        }        
        
        $this->api->setFilter('topic_id', $topic['id']);
        $this->api->setFilter('message_type', OrmSupport::TYPE_ADMIN_SYSTEM_MESSAGE, '!=');
        $this->api->setOrder('dateof');
        $list = $this->api->getList();
        $this->api->markViewedList($topic['id'], true);
        
        $this->view->assign([
            'supp' => $support_item,
            'topic' => $topic,
            'list' => $list
        ]);
        return $this->result->setTemplate( 'support.tpl' );        
    }

    /**
     * Удаляет тему переписки
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDelTopic()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $this->topic_api->setFilter('id', $id);
        $this->topic_api->setFilter('user_id', $this->user['id']);
        $topic = $this->topic_api->getFirst();
        
        if ($topic) {
            $topic->delete();
        }
        
        return $this->result
                        ->setSuccess(true)
                        ->setNoAjaxRedirect($this->router->getUrl('support-front-support'));
    }
}