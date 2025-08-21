<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Orm;
use Alerts\Model\Manager as AlertManager;
use RS\Config\Loader;
use RS\Helper\Tools;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Support\Model\Notice\Topic as TopicNotice;
use Support\Model\Platform\AbstractPlatform;
use Support\Model\Platform\Manager as PlatformManager;
use Support\Model\Platform\PlatformSite;
use Users\Model\Orm\User;

/**
 * Тема, группирующая сообщения в поддержку
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $platform Платформа
 * @property string $platform_data Произвольные данные платформы в формате JSON
 * @property string $title Тема
 * @property string $number Уникальный номер темы
 * @property integer $user_id Пользователь
 * @property string $user_email Email (для неавторизованного пользователя)
 * @property string $user_name Имя пользователя (для неавторизованного пользователя)
 * @property integer $manager_id Ответственный менеджер
 * @property string $created Дата создания
 * @property string $updated Дата обновления
 * @property string $last_messages_request Последний запрос сообщений в данной теме
 * @property integer $msgcount Всего сообщений
 * @property integer $newcount Новых сообщений
 * @property integer $newadmcount Новых для администратора
 * @property string $status Статус
 * @property string $external_id Идентификатор темы во внешней системе
 * @property string $comment Комментарий администратора
 * @property string $_first_message_ Сообщение
 * --\--
 */
class Topic extends OrmObject
{
    const STATUS_OPEN = 'open';
    const STATUS_ANSWERED = 'answered';
    const STATUS_CLOSED = 'closed';

    protected static
        $table = 'support_topic';

    /**
     * @var self текущий объект до сохранения
     */
    public $before;


    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'platform' => new Type\Varchar([
                'description' => t('Платформа'),
                'hint' => t('Платформа, на которой идет переписка - сайт, telegram, ....'),
                'list' => [['Support\Model\Platform\Manager', 'getPlatfromTitles']],
                'checker' => ['chkEmpty', t('Платформа должна быть обязательно задана')],
                'visible' => false,
                'listenPost' => false
            ]),
            'platform_data' => new Type\Text([
                'description' => t('Произвольные данные платформы в формате JSON'),
                'visible' => false,
                'listenPost' => false
            ]),
            'title'   => new Type\Varchar([
                'description' => t('Тема'),
                'checker' => ['chkEmpty', t('Не указана тема')],
                'meVisible' => false,
            ]),
            'number' => new Type\Varchar([
                'description' => t('Уникальный номер темы'),
                'maxLength' => 20,
                'unique' => true,
                'visible' => false,
                'meVisible' => false,
                'appVisible' => true,
            ]),
            'user_id' => new Type\User([
                'description' => t('Пользователь'),
                'checker' => [function($_this, $value) {
                    if ($_this['platform'] == PlatformSite::PLATFORM_ID && !$value) {
                        return t('Необходимо указать пользователя, с которым будет переписка');
                    }
                    return true;
                }]
            ]),
            'user_email' => new Type\Varchar([
                'description' => t('Email (для неавторизованного пользователя)'),
                'visible' => false,
                'emailVisible' => true
            ]),
            'user_name' => new Type\Varchar([
                'description' => t('Имя пользователя (для неавторизованного пользователя)'),
                'visible' => false,
                'telegramVisible' => true
            ]),
            'manager_id' => new Type\User([
                'description' => t('Ответственный менеджер'),
            ]),
            'created' => new Type\Datetime([
                'description' => t('Дата создания'),
                'visible' => false,
                'appVisible' => true,
            ]),
            'updated' => new Type\Datetime([
                'description' => t('Дата обновления'),
                'visible' => false,
                'appVisible' => true,
            ]),
            'last_messages_request' => new Type\Datetime([
                'visible' => false,
                'description' => t('Последний запрос сообщений в данной теме'),
            ]),
            'msgcount' => new Type\Integer([
                'visible' => false,
                'appVisible' => true,
                'description' => t('Всего сообщений'),
            ]),
            'newcount' => new Type\Integer([
                'visible' => false,
                'appVisible' => true,
                'description' => t('Новых сообщений'),
            ]),
            'newadmcount' => new Type\Integer([
                'visible' => false,
                'description' => t('Новых для администратора'),
            ]),
            'status' => new Type\Enum(array_keys(self::getStatusesTitles()), [
                'description' => t('Статус'),
                'default' => self::STATUS_OPEN,
                'allowEmpty' => false,
                'list' => [[__CLASS__, 'getStatusesTitles']]
            ]),
            'external_id' => new Type\Varchar([
                'description' => t('Идентификатор темы во внешней системе'),
                'maxLength' => 100,
                'visible' => false
            ]),
            'comment' => new Type\Text([
                'description' => t('Комментарий администратора'),
                'visible' => false,
            ]),
            '_first_message_' => new Type\Richtext([
                'description' => t('Сообщение'),
                'editorOptions' => [[
                    'tiny_options' => [
                        'plugins'            => ["link", "searchreplace visualblocks code emoticons", "paste"],
                        'toolbar1'           => 'undo | bold italic underline | removeformat | cut copy paste | searchreplace | link unlink code emoticons',
                        'toolbar2'           => '',
                    ]
                ]],
                'runtime' => true,
                'visible' => false,
            ])
        ]);

        $this->addIndex(['site_id', 'external_id'], self::INDEX_UNIQUE);
    }

    /**
     * Обработчик перед сохранением объекта
     *
     * @param string $save_flag
     * @return false|void
     */
    public function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG) {
            if (!$this['status']) {
                $this['status'] = self::STATUS_OPEN;
            }
            $this['created'] = date('c');
            $this['last_messages_request'] = date('Y-m-d H:i:s');
            $this['number'] = $this->generateNumber();

            if ($this['user_id'] > 0 && !$this['user_email']) {
                $this['user_email'] = $this->getUser()->e_mail;
            }
        }

        if ($save_flag == self::UPDATE_FLAG) {
            $this->before = new self($this['id']);
        }

        if ($this['external_id'] === '') {
            $this['external_id'] = null;
        }
    }

    /**
     * Устанавливает произвольные данные, необходимые для платформы
     *
     * @param array $data
     * @return Topic
     */
    public function setPlatformData(array $data)
    {
        $this['platform_data'] = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * Возвращает произвольные данные платформы
     *
     * @param null|string $key Если null, то будет возвращен весь массив данных
     * @param null $default значение по умолчанию, на случай отсутствия данных
     * @return array
     */
    public function getPlatformData($key = null, $default = null)
    {
        $data = json_decode((string)$this['platform_data'], true) ?: [];

        if ($key !== null) {
            return $data[$key] ?? $default;
        }
        return $data;
    }

    /**
     * Генерирует уникальный номер
     *
     * @return string
     */
    public function generateNumber()
    {
        $config = Loader::byModule($this, $this['__site_id']->get());
        $numbers = $config['number_mask_digits'] ?: 6;
        $number = Tools::generatePassword($numbers, '0123456789');

        //Посмотрим есть ли такой код уже в базе
        $found = Request::make()
            ->from($this)
            ->where([
                'number' => $number,
            ])
            ->count();

        if ($found) {
            return $this->generateNumber();
        }

        return $number;
    }


    /**
     * Возвращает список названий статусов тикетов
     *
     * @param array $first Массив, добавляемый в начало списка
     * @return array
     */
    public static function getStatusesTitles(array $first = [])
    {
        $result = [];
        foreach(self::getStatuses() as $id => $item) {
            $result[$id] = $item['title'];
        }

        return $first + $result;
    }

    /**
     * Возвращает массив возможных статусов
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => [
                'title' => t('Открыт'),
                'background' => '#099fc2',
                'color' => 'white'
            ],
            self::STATUS_ANSWERED => [
                'title' => t('Отвечен'),
                'background' => '#006600',
                'color' => 'white'
            ],
            self::STATUS_CLOSED => [
                'title' => t('Закрыт'),
                'background' => '#999999',
                'color' => 'white'
            ],
        ];
    }

    /**
     * Обработчик сохранения темы
     *
     * @param string $flag
     */
    function afterWrite($flag)
    {
        if($this['_first_message_']){
            $support_message = new Support;
            $support_message->topic_id  = $this->id;
            $support_message->user_id = $this->user_id;
            $support_message->message = $this['_first_message_'];
            $support_message->dateof  = date('Y-m-d H:i:s');

            //Для первого сообщения существует уведомление о создании тикета
            $support_message->is_first_topic_message = true;
            if($this['_admin_creation_']) {
                $support_message->user_id = \RS\Application\Auth::getCurrentUser()->id;
                $support_message->is_admin = 1;
                $support_message->message_type = Support::TYPE_ADMIN_MESSAGE;
                $support_message->message_format = Support::MESSAGE_FORMAT_HTML;
            } else {
                $support_message->message_type = Support::TYPE_USER_MESSAGE;
                $support_message->message_format = Support::MESSAGE_FORMAT_TEXT;
            }

            $support_message->insert();
        }

        //Вызываем обработчик платформы.
        if ($platform = $this->getPlatform()) {
            $platform->onSaveTicket($this, $flag);
        }
    }


    /**
     * Обработчик удаления темы
     *
     * @return bool
     */
    function delete()
    {
        $delete_result = parent::delete();

        if ($delete_result) {
            $q = new Request();
            $q->delete()
                ->from(new Support())
                ->where( ['topic_id' => $this['id']])
                ->exec();
        }
        
        return $delete_result;
    }

    /**
     * Возвращает пользователя-автора темы обращения
     *
     * @return User
     */
    function getUser()
    {
        return new User($this['user_id']);
    }

    /**
     * Возвращает первое сообщение из переписки
     *
     * @return Support
     */
    function getFirstMessage()
    {
        $api = new \Support\Model\Api();
        $api->setFilter('topic_id', $this['id']);
        $api->setOrder('id');
        return $api->getFirst();
    }

    /**
     * Возвращает ссылку на страницу переписки
     *
     * @param bool $absolute
     * @return string
     */
    function getUrl($absolute = false)
    {
        return RouterManager::obj()->getUrl('support-front-support', [
            "Act" => "viewTopic",
            "number" => $this['number']
        ], $absolute);
    }

    /**
     * Возвращает объект платформы, на которой ведется переписка
     *
     * @return AbstractPlatform
     */
    public function getPlatform()
    {
        $platform = PlatformManager::getPlatformById($this['platform']);
        $platform->initPlatformData($this->getPlatformData());
        $platform->initTopic($this);
        return $platform;
    }

    /**
     * Возвращает список сообщений текущей переписки для административной панели
     *
     * @return array
     */
    public function getMessagesForAdmin()
    {
        return Request::make()
            ->from(new Support())
            ->where([
                'topic_id' => $this['id']
            ])
            ->where("message_type != '".Support::TYPE_USER_SYSTEM_MESSAGE."'")
            ->orderby('id')
            ->objects();
    }

    /**
     * Возвращает массив со сведениями о статусе
     *
     * @return array
     */
    public function getStatus()
    {
        $statuses = self::getStatuses();
        return $statuses[$this['status']];
    }

    /**
     * Возвращает объект пользователя-менеджера
     *
     * @return User
     */
    public function getManagerUser()
    {
        return new User($this['manager_id']);
    }

    /**
     * Возвращает общее количество тикетов
     *
     * @return integer
     */
    public function getUserTotalTicketCount()
    {
        $q = Request::make()
            ->from($this);

        if ($this['user_id'] > 0) {
            $q->where([
                'user_id' => $this['user_id']
            ]);
        } else {
            $q->where([
                'user_email' => $this['user_email']
            ]);
        }

        return $q->count();
    }

    /**
     * Возвращает либо ФИО, либо любую другую информацию идентификационную, которая есть
     *
     * @return string
     */
    public function getUserName()
    {
        if ($this['user_id']) {
            return $this->getUser()->getFio()." ({$this['user_id']})";
        } elseif ($this['user_name']) {
            return $this['user_name'];
        } elseif ($this['user_email']) {
            return $this['user_email'];
        } else {
            return t('Пользователь не определен');
        }
    }

    /**
     * Возвращает ID последнего сообщения переписки
     *
     * @return integer
     */
    public function getLastMessageId()
    {
        return Request::make()
            ->select('id')
            ->from(new Support())
            ->where([
                'topic_id' => $this['id']
            ])
            ->orderby('id desc')
            ->limit(1)
            ->exec()
            ->getOneField('id', 0);
    }

    /**
     * Возвращает последнее сообщение переписки
     */
    public function getLastMessage()
    {
        return Request::make()
            ->from(new Support())
            ->where([
                'topic_id' => $this['id']
            ])
            ->orderby('id desc')
            ->limit(1)
            ->object();
    }
}