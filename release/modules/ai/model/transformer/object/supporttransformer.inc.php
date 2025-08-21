<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer\Object;

use Ai\Model\Transformer\AbstractField;
use Ai\Model\Transformer\AbstractTransformer;
use Ai\Model\Transformer\Field\HtmlField;
use Ai\Model\Transformer\ReplaceVariable;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use RS\Router\Manager;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;
use Support\Model\TopicApi;

/**
 * Класс, позволяющий генерировать ответ в поддержке в 1 клик
 */
class SupportTransformer extends AbstractTransformer
{

    /**
     * Возвращает идентификатор транформера
     *
     * @return string
     */
    public static function getId()
    {
        return 'support-topic';
    }

    /**
     * Возвращает название транформера
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('Поддержка');
    }

    /**
     * Возвращает список полей, которые могут автоматически заполняться с помощью ИИ
     *
     * @return AbstractField[]
     */
    protected function initFields()
    {
        return [
            new HtmlField($this, 'message', t('Сообщение'), [
                'toolbarNumber' => 1
            ])
        ];
    }

    /**
     * Возвращает список объектов для замены переменных
     *
     * @return ReplaceVariable[]
     */
    public function getVariables()
    {
        $topic = new ReplaceVariable();
        $topic->title = t('Тема переписки');
        $topic->name = 'topic.title';
        $topic->key = 'title';
        $topic->value = $this->getSourceObjectTitle();

        $messages = $this->getCorrespondence();

        $messages_all = new ReplaceVariable();
        $messages_all->title = t('Вся переписка');
        $messages_all->name = 'messages.all';
        $messages_all->key = 'messages.all';
        $messages_all->value = $messages['all'];

        $messages_client = new ReplaceVariable();
        $messages_client->title = t('Все сообщения клиента');
        $messages_client->name = 'messages.client';
        $messages_client->key = 'messages.client';
        $messages_client->value = $messages['client'];

        $messages_admin = new ReplaceVariable();
        $messages_admin->title = t('Все сообщения администратора');
        $messages_admin->name = 'messages.admin';
        $messages_admin->key = 'messages.admin';
        $messages_admin->value = $messages['admin'];

        return [
            $topic->name => $topic,
            $messages_all->name => $messages_all,
            $messages_client->name => $messages_client,
            $messages_admin->name => $messages_admin
        ];
    }

    /**
     * Возвращает всю переписку по одному тикету
     *
     * @return array
     */
    protected function getCorrespondence()
    {
        $result = [
            'all' => '',
            'client' => '',
            'admin' => ''
        ];
        if ($this->source_object['id']) {
            $messages = Request::make()
                ->from(new Support())
                ->where([
                    'topic_id' => $this->source_object['id']
                ])
                ->whereIn("message_type", [Support::TYPE_ADMIN_MESSAGE, Support::TYPE_USER_MESSAGE])
                ->orderby('id')
                ->objects();

            foreach($messages as $message) {
                $result['all'] .= '- '
                    .($message['is_admin'] ? t('Специалист поддержки: ') : t('Клиент: '))
                    ."`{$message['message']}`\n";

                if ($message['is_admin']) {
                    $result['admin'] .= "{$message['message']}\n\n";
                } else {
                    $result['client'] .= "{$message['message']}\n\n";
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает объект класса для выборки объектов, заполняемых данным трансформером
     *
     * @return EntityList
     */
    public function getDaoObject()
    {
        return new TopicApi();
    }

    /**
     * Устанавливает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param mixed $object
     * @return void
     */
    public function setSourceData($object)
    {
        $this->source_object = $object;
    }

    /**
     * Загружает по ID исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param integer $id
     * @return void
     */
    public function setSourceDataById($id)
    {
        $this->source_object = new Topic($id);
    }

    /**
     * Возвращает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->source_object;
    }

    /**
     * Возвращает ссылку на просмотр/редактирование объекта в административной панели
     *
     * @return string
     */
    public function getSourceObjectAdminUrl()
    {
        $router = Manager::obj();
        return $router->getAdminUrl(false, [
            'id' => $this->source_object['id']
        ], 'support-supportctrl');
    }

    /**
     * Возвращает название исходного объекта
     *
     * @return string
     */
    public function getSourceObjectTitle()
    {
        return $this->source_object['title'] ?? '';
    }

    /**
     * Возвращает исходный для заполнения объект из $post_array
     *
     * @param array $post_array
     * @return mixed
     */
    public function fillSourceObjectPromPost(array $post_array)
    {
        $topic = new Topic($post_array['topic_id'] ?? 0);
        $this->setSourceData($topic);
    }
}