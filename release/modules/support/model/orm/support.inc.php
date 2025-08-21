<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Orm;
use Alerts\Model\Manager;
use Alerts\Model\Types\AbstractNotice;
use Files\Model\FileApi;
use Files\Model\Orm\File;
use Files\Model\OrmType\Files;
use RS\Config\Loader;
use RS\Db\Adapter;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Orm\Type\Richtext;
use Support\Model\FilesType\SupportFiles;
use Support\Model\Notice\NewPostToAdmin;
use Support\Model\Notice\NewTopicToAdmin;
use Support\Model\Notice\Post;
use Users\Model\Orm\User;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $topic Тема
 * @property integer $user_id Пользователь
 * @property string $dateof Дата создания
 * @property string $updated Дата изменения сообщения клиентом
 * @property string $message Сообщение
 * @property string $message_format Формат сообщения
 * @property integer $processed Флаг прочтения
 * @property integer $is_admin Это администратор
 * @property integer $topic_id ID темы
 * @property string $topic_platform Идентификатор платформы, с которой связана тема
 * @property string $message_type Тип сообщения
 * @property string $message_data Произвольные данные сообщения (json)
 * @property string $external_id Внешний ID
 * @property integer $is_delivered Доставлено
 * @property integer $mediagroup_id Идентификатор группы медиа ресурсов (для мессенджеров)
 * --\--
 */
class Support extends OrmObject
{
    const TYPE_USER_MESSAGE = 'user_message';                 //Сообщение от пользователя
    const TYPE_USER_SYSTEM_MESSAGE = 'user_system_message';   //Техническое сообщение для пользователя
    const TYPE_ADMIN_MESSAGE = 'admin_message';               //Сообщение от администратора
    const TYPE_ADMIN_SYSTEM_MESSAGE = 'admin_system_message'; //Техническое сообщение для администратора

    const MESSAGE_FORMAT_TEXT = 'text';
    const MESSAGE_FORMAT_HTML = 'html';

    protected static
        $table = 'support';
    
    protected static $user_cache;

    /**
     * @var self текущий объект до сохранения
     */
    public $before;
        
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'topic'   => new Type\Varchar([
                    'description' => t('Тема'),
                    'checker' => [[__CLASS__, 'checkTopic'], t('Укажите, пожалуйста, тему')],
                    'runtime' => true,
                    'visible' => false
                ]),
                'user_id' => new Type\Integer([
                    'description' => t('Пользователь'),
                    'visible' => false,
                ]),
                'dateof' => new Type\Datetime([
                    'description' => t('Дата создания'),
                ]),
                'updated' => new Type\Datetime([
                    'description' => t('Дата изменения сообщения клиентом')
                ]),
                'message' => new Type\Text([
                    'description' => t('Сообщение'),
                    'checker' => ['chkEmpty', t('Не задано сообщение')],
                ]),
                'message_format' => new Type\Enum([self::MESSAGE_FORMAT_TEXT, self::MESSAGE_FORMAT_HTML], [
                    'description' => t('Формат сообщения'),
                    'default' => self::MESSAGE_FORMAT_TEXT,
                    'listFromArray' => [[
                        self::MESSAGE_FORMAT_TEXT => t('Текст'),
                        self::MESSAGE_FORMAT_HTML => t('HTML')
                    ]],
                    'allowEmpty' => false,
                    'visible' => false
                ]),
                'processed' => new Type\Integer([
                    'maxLength'   => 1,
                    'allowEmpty' => false,
                    'description' => t('Флаг прочтения'),
                    'visible' => false
                ]),
                'is_admin' => new Type\Integer([
                    'maxLength'   => 1,
                    'description' => t('Это администратор'),
                    'visible' => false,
                    'appVisible' => true
                ]),
                'topic_id' => new Type\Integer([
                    'description' => t('ID темы'),
                    'visible' => false
                ]),
                'topic_platform' => new Type\Varchar([
                    'description' => t('Идентификатор платформы, с которой связана тема'),
                    'visible' => false,
                    'runtime' => true
                ]),
                'message_type' => new Type\Varchar([
                    'description' => t('Тип сообщения'),
                    'maxLength' => 50,
                    'list' => [[__CLASS__, 'getMessageTypeTitles']],
                    'visible' => false,
                    'listenPost' => false
                ]),
                'message_data' => new Type\Text([
                    'description' => t('Произвольные данные сообщения (json)'),
                    'visible' => false,
                    'listenPost' => false
                ]),
                'external_id' => new Type\Varchar([
                    'description' => t('Внешний ID'),
                    'maxLength' => 100
                ]),
                'is_delivered' => new Type\Integer([
                    'description' => t('Доставлено'),
                    'hint' => t('Используется, если сообщения доставляются во внешнюю площадку(платформу)'),
                    'visible' => false
                ]),
                'mediagroup_id' => new Type\Integer([
                    'description' => t('Идентификатор группы медиа ресурсов (для мессенджеров)'),
                    'visible' => false
                ]),
            t('Вложения'),
                'attachments' => (new Files())
                    ->setDescription(t('Файлы'))
                    ->setLinkType(SupportFiles::getShortName())
        ]);

        $this->addIndex(['site_id', 'external_id'], self::INDEX_UNIQUE);
        $this->addIndex(['topic_id', 'message_type'], self::INDEX_KEY);
    }

    /**
     * Устанавливает произвольные данные для сообщения
     *
     * @param array $data
     * @return self
     */
    public function setMessageData(array $data)
    {
        $this['message_data'] = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * Возвращает HTML-код сообщения для отображения
     * @return string
     */
    function getMessage()
    {
        if ($this['message_format'] == self::MESSAGE_FORMAT_HTML) {
            return $this['message'];
        }
        return nl2br($this['message']);
    }

    /**
     * Возвращает произвольные данные сообщения
     *
     * @param null|string $key Если null, то будет возвращен весь массив данных
     * @param null $default значение по умолчанию, на случай отсутствия данных
     * @return array
     */
    public function getMessageData($key = null, $default = null)
    {
        $data = json_decode((string)$this['message_data'], true) ?: [];

        if ($key !== null) {
            return $data[$key] ?? $default;
        }
        return $data;
    }

    /**
     * Добавляет произвольные данные к сообщению
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addMessageData($key, $value)
    {
        $data = $this->getMessageData();
        $data[$key] = $value;
        $this->setMessageData($data);
        return $this;
    }

    /**
     * Возвращает вложения, связанные с сообщением
     *
     * @return File[]
     */
    public function getAttachments()
    {
        return FileApi::getLinkedFiles(SupportFiles::getShortName(),
            $this['id'],
            SupportFiles::ACCESS_TYPE_VISIBLE);
    }

    /**
     * Возвращает названия типов сообщений
     *
     * @return array
     */
    public static function getMessageTypeTitles()
    {
        return [
            self::TYPE_USER_MESSAGE => t('Сообщение от пользователя'),
            self::TYPE_USER_SYSTEM_MESSAGE => t('Техническое сообщение для пользователя'),
            self::TYPE_ADMIN_MESSAGE => t('Сообщение от администратора'),
            self::TYPE_ADMIN_SYSTEM_MESSAGE => t('Техническое сообщение для администратора')
        ];
    }

    /**
     * Валидирует название темы обращения
     *
     * @param self $_this
     * @param string $value
     * @param string $error
     * @return bool
     */
    public static function checkTopic($_this, $value, $error)
    {
        if ($_this['topic_id'] == 0 && !$_this['topic']) {
            return $error;
        }
        return true;
    }

    /**
     * Обработчик перед сохранением объекта
     *
     * @param string $flag
     * @return false|void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
                $this['dateof'] = date('Y-m-d H:i:s');
                $this['processed'] = 0;
        }
        
        if ($this['topic_id'] == 0 && !empty($this['topic'])) {
            //Сздаем тему, при необходимости
            $topic = new Topic();
            $topic['title'] = $this['topic'];
            $topic['platform'] = $this['topic_platform'];
            $topic['user_id'] = $this['user_id'];
            $topic['updated'] = $this['dateof'];
            $topic['msgcount'] = 1;
            $topic['newcount'] = 0;
            $topic->insert();
            
            $this['topic_id'] = $topic['id'];
            $this['is_first_topic_message'] = true;
        }

        if ($this['external_id'] === '') {
            $this['external_id'] = null;
        }

        if ($flag == self::UPDATE_FLAG) {
            $this->before = new self($this['id']);
        }
    }

    /**
     * Обработчик после сохранения объектоа
     *
     * @param string $flag
     */
    function afterWrite($flag)
    {
        $topic = $this->getTopic();
        $topic['status'] = $this['is_admin'] ? Topic::STATUS_ANSWERED : Topic::STATUS_OPEN;
        $topic->update();

        //Привязывает вложения к сообщению
        if ($this->isModified('attachments')) {
            foreach($this['attachments'] as $public_hash) {
                $file = File::loadByUniq($public_hash);
                if ($file['id'] && $file['link_id'] != $this['id']) {
                    $file['link_id'] = $this['id'];
                    $file->update();
                }
            }
        }

        $this->updateTopicCounts();

        //Отправляем сообщения администратору от пользователя
        if ($flag == self::INSERT_FLAG && !$this['is_admin'] && !$this['dont_send_admin_notice']) {

            if ($this['is_first_topic_message']) {
                $notice = new NewTopicToAdmin();
                $notice->init($this);
                Manager::send($notice);
            } else {
                $notice = new NewPostToAdmin();
                $notice->init($this);
                Manager::send($notice);
            }
        }

        //Вызываем обработчик платформы. Он может, например, отправить сообщение через telegram или на почту.
        if ($platform = $topic->getPlatform()) {
            $platform->onSaveMessage($this, $flag);
        }
    }

    /**
     * Обновляет суммарные цифры новых сообщений для администратора и клиента
     */
    function updateTopicCounts()
    {
        //Обновляем счетчики у темы
        $q = new Request();
        //Общее количество
        $total_msg = $q->from($this)
                        ->where( ['topic_id' => $this['topic_id']])
                        ->count();
        
        //Новые для пользователей (которые написал администратор)
        $q = new Request();
        $new_msg = $q->from($this)
                    ->where([
                        'topic_id' => $this['topic_id'],
                        'is_admin' => '1',
                        'processed' => '0'
                    ])->count();
                    
        //Новые для администратора
        $q = new Request();
        $new_admin_msg = $q->from($this)
                    ->where([
                        'topic_id' => $this['topic_id'],
                        'is_admin' => '0',
                        'processed' => '0'
                    ])->count();
                    
        $q = new Request();
        $updated = $q->select("dateof")
                        ->from($this)
                        ->where( ['topic_id' => $this['topic_id']])
                        ->orderby("dateof DESC")
                        ->limit(1)
                        ->exec()
                        ->getOneField("dateof");

        Request::make()
            ->update(new Topic())
            ->set([
                'msgcount' => $total_msg,
                'newcount' => $new_msg,
                'newadmcount' => $new_admin_msg,
                'updated' => $updated
            ])
            ->where([
                'id' => $this['topic_id']
            ])
            ->exec();
    }

    /**
     * Обработчик удаления объекта
     *
     * @param bool $updateCounter
     * @return bool
     */
    function delete($updateCounter = true)
    {
        if (empty($this['topic_id']) && !empty($this['id'])) $this->load($this['id']);
        if ($ret = parent::delete() && $updateCounter) {
            $this->updateTopicCounts();
            foreach($this->getAttachments() as $file) {
                $file->delete();
            }
        }
        return $ret;
    }

    /**
     * Возвращает объект темы обращения
     *
     * @return Topic
     */
    function getTopic()
    {
        $topic = Topic::loadSingle($this['topic_id']);
        return $topic;
    }

    /**
     * Возвращает объект пользователя - создателя сообщения
     *
     * @return User
     */
    function getUser()
    {
        if (!isset(self::$user_cache[$this['user_id']])) {
            self::$user_cache[$this['user_id']] = new User($this['user_id']);
        }
        return self::$user_cache[$this['user_id']]; 
    }

    /**
     * Возвращает имя пользователя, оставивщего сообщение
     *
     * @return string
     */
    function getUserName()
    {
        if ($this->isSystemMessage()) {
            return t('Автоматическое сообщение');
        } else {
            $user = $this->getUser();
            if ($user['id']) {
                return $user->getFio();
            } else {
                return $this->getTopic()->getUserName();
            }
        }
    }

    /**
     * Заменяет поле сообщение, визуальным редактором
     *
     * @param string $link_type Тип ссылки на товары и категории, который будет вставляться в визуальном редакторе.
     * Зависит от того, какой тикет будет открыт. Бывает тикет, созданный на сайте и через Telegram
     */
    public function setMessageRichText($link_type = '')
    {
        $router = \RS\Router\Manager::obj();

        $this->getPropertyIterator()->append([
            'message' => (new RichText())
                ->setDescription('Сообщение')
                ->setName('message')
                ->setEditorOptions([
                    'tiny_options' => [
                        'plugins'            => ["link", "searchreplace visualblocks code emoticons", "paste", "rsproductlink", "rscategorylink"],
                        'toolbar1'           => 'undo | bold italic underline | removeformat | cut copy paste | searchreplace | link unlink code emoticons | rsproductlink rscategorylink',
                        'toolbar2'           => '',
                        'rsProductDialog'    => [
                            'url' => [
                                'getChild' => $router->getAdminUrl('getChildCategory', [], 'catalog-dialog'),
                                'getProducts' => $router->getAdminUrl('getProducts', [], 'catalog-dialog'),
                                'getDialog' => $router->getAdminUrl(false, [], 'catalog-dialog'),
                                'getProductLink' => $router->getAdminUrl('getProductLink', [], 'catalog-dialog'),
                                'getCategoryLink' => $router->getAdminUrl('getCategoryLink', [], 'catalog-dialog'),
                            ],
                            'linkType' => $link_type
                        ],
                        'relative_urls' => false,
                        'remove_script_host' => false,
                    ]
                ])
        ]);
    }

    /**
     * Возвращает true, если сообщение системное (автоматическое)
     *
     * @return bool
     */
    public function isSystemMessage()
    {
        return in_array($this['message_type'], [
            self::TYPE_USER_SYSTEM_MESSAGE,
            self::TYPE_ADMIN_SYSTEM_MESSAGE
        ]);
    }
}