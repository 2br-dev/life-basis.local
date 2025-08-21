<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Platform;

use RS\Orm\FormObject;
use RS\View\Engine;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;

/**
 * Базовый класс платформы для переписки.
 * Платформой может являться, например - сайт, телеграм, электронная почта
 */
abstract class AbstractPlatform
{
    const TINY_LINK_TYPE_SITE = 'site';

    private $data = [];
    protected $user_info_tpl = '%support%/admin/topic_view_user.tpl';
    protected $topic;

    /**
     * Возвращает идентификатор платформы
     *
     * @return string
     */
    abstract function getId();

    /**
     * Возвращает название платформы
     *
     * @return string
     */
    abstract function getTitle();

    /**
     * Обработчик сохранения сообщения
     *
     * @param Support $message Объект сообщения
     * @param string $save_flag флаг операции
     * RS\Orm\AbstractObject::INSERT_FLAG,
     * RS\Orm\AbstractObject::UPDATE_FLAG,
     * RS\Orm\AbstractObject::REPLACE_FLAG
     *
     * @return void
     */
    public function onSaveMessage(Support $message, $save_flag)
    {}

    /**
     * Обработчик сохранения тикета
     *
     * @param Topic $topic Объект темы переписки
     * @param string $save_flag флаг операции
     * RS\Orm\AbstractObject::INSERT_FLAG,
     * RS\Orm\AbstractObject::UPDATE_FLAG,
     * RS\Orm\AbstractObject::REPLACE_FLAG
     *
     * @return void
     */
    public function onSaveTicket(Topic $topic, $save_flag)
    {}

    /**
     * Загружает произвольные данные платформы
     *
     * @param array $data
     */
    public function initPlatformData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Загружает объект тикета к данному объекту платформы
     *
     * @param Topic $topic
     */
    public function initTopic(Topic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * Возвращает объект привязанного тикета
     *
     * @return Topic|null
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Возвращает произвольные данные платформы
     *
     * @param null|mixed $key ключ массива
     * @param null|mixed $default значение по умолчанию
     * @return mixed
     */
    public function getPlatformData($key = null, $default = null)
    {
        if ($key !== null) {
            return $this->data[$key] ?? $default;
        }
        return $this->data;
    }

    /**
     * Возвращает Набор публичных данных, которые необходимо отобразить при просмотре
     * тикета в административной панели
     *
     * @return array
     * [
     *   [
     *      'title' => 'Параметр',
     *      'value' => 'Значение'
     *   ]
     * ]
     */
    public function getPublicData()
    {
        return [];
    }

    /**
     * Возвращает true, если тикеты данной платформы допустимо отображать в личном кабинете на сайте у пользователя
     *
     * @return bool
     */
    public function canShowOnSite()
    {
        return true;
    }

    /**
     * Возвращает информацию о пользователе на странице просмотра тикета в административной панели
     *
     * @param Topic $topic
     * @return string
     */
    public function getUserInfoHtml(Topic $topic)
    {
        $view = new Engine();
        $view->assign([
            'topic' => $topic
        ]);
        return $view->fetch($this->user_info_tpl);
    }

    /**
     * Возвращает тип ссылки, который необходимо вернуть в визуальном редакторе для данного тикета
     *
     * @return string
     */
    public function getTinyLinkType()
    {
        return static::TINY_LINK_TYPE_SITE;
    }
}