<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

use RS\Config\Loader;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;
use Users\Model\Orm\User;

/**
 * API для работы с сообщениями тикетов
 */
class Api extends EntityList
{
        
    function __construct()
    {
        parent::__construct(new Support,
        [
            'defaultOrder' => 'dateof DESC',
            'multisite' => true
        ]);
    }

    /**
     * Помечает сообщения прочитанными.
     *
     * @param integer $topic_id ID Тикета
     * @param bool $is_admin_messages Если true, то отметит прочитанными сообщения от администратора
     */
    function markViewedList($topic_id, $is_admin_messages)
    {
        $is_admin = (int)$is_admin_messages;
        Request::make()
            ->update($this->obj_instance)
            ->set([
                'processed' => 1
            ])
            ->where([
                'is_admin' => $is_admin,
                'topic_id' => $topic_id
            ])->exec();
            
        $field = $is_admin ? 'newcount' : 'newadmcount';
        //Обновляем счетчики
        $sub_query = Request::make()
            ->select('COUNT(*)')
            ->from($this->obj_instance, 'S')
            ->where('S.topic_id = ST.id')
            ->where([
                'processed' => 0,
                'is_admin' => $is_admin
            ]);

        Request::make()
            ->update(new Topic())->asAlias('ST')
            ->set([
                "ST.$field" => $sub_query->toSql()
            ])
            ->where([
                'ST.id' => $topic_id
            ])
            ->exec();
    }

    /**
     * Возвращает суммарное количество новых сообщений по всем темам пользователя $user_id
     *
     * @param integer $user_id
     * @return mixed
     */
    function getNewMessageCount($user_id)
    {
        $q = new Request();
        $sum = $q->select('SUM(newcount) as sum')
            ->from(new Topic())
            ->where( ['user_id' => $user_id])
            ->whereIn('platform', TopicApi::getUserAccountPlatForms())
            ->exec()
            ->getOneField('sum', 0);
        
        return $sum;
    }

    /**
     * Возвращает объект сообщения по external_id
     *
     * @param string $external_id
     */
    function getSupportByExternalId($external_id)
    {
        return Request::make()
            ->from(new Support())
            ->where( ['external_id' => $external_id])
            ->object();
    }

    /**
     * Возвращает список менеджеров
     *
     * @return User[]
     */
    function getManagers()
    {
        $config = Loader::byModule($this);
        if ($config['manager_group']) {
            $api = new \Users\Model\Api();
            $api->setFilter('group', $config['manager_group']);
            return $api->getAssocList('id');
        }

        return [];
    }
}

