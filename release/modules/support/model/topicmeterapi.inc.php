<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

use Main\Model\NoticeSystem\MeterApiInterface;
use Support\Model\Orm\Topic;

/**
 * Класс предоставляет API связанные со счетчиком для CRUD контроллера
 */
class TopicMeterApi implements MeterApiInterface
{
    protected
        $topic_api;

    function __construct(TopicApi $topic_api)
    {
        $this->topic_api = $topic_api;
    }

    /**
     * Возвращает идентификатор счетчика
     *
     * @return string
     */
    function getMeterId()
    {
        return 'rs-admin-menu-support';
    }

    /**
     * Возвращает количество открытых тем поддержки
     *
     * @param integer|null $user_id
     * @return integer
     */
    function getUnviewedCounter()
    {
        $q = clone $this->topic_api->queryObj();
        $q->where([
            'status' => Topic::STATUS_OPEN
        ]);
        return $q->count();
    }

    /**
     * Заглушка. Отмечает просмотренным один объект
     * Возвращает количество непросмотренных объектов
     *
     * @param array $ids
     * @return integer
     */
    function markAsViewed($ids)
    {
        return 0;
    }

    /**
     * Заглушка. Отмечает просмотренными все объекты
     *
     * @param integer|null $user_id
     * @return integer
     */
    function markAllAsViewed()
    {
        return 0;
    }

    /**
     * Удаляет сведения о просмотрах объектов
     *
     * @return bool
     */
    function removeViewedFlag($ids)
    {
        return 0;
    }
}