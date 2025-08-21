<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Antivirus\Model;


use Antivirus\Config\File;
use Antivirus\Model\Orm\Event;
use Antivirus\Model\Orm\RequestCount;
use RS\Config\Loader;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

class RequestCountApi extends EntityList
{
    /** @var File */
    private $config;

    function __construct()
    {
        parent::__construct(new RequestCount());
        $this->config = Loader::byModule($this);
    }


    /**
     * Возвращает допустимый интервал в милисекундах между запросами, в соответсвии с настройками
     *
     * @return int
     */
    public function getAllowedRequestInterval()
    {
        return (int) $this->config->proactive_allowed_interval;
    }

    /**
     * Возвращает количество подряд идущих запросов, при котором происходит блокировка
     *
     * @return int
     */
    private function getTriggerRequestCount()
    {
        return (int) $this->config->proactive_trigger_request_count;
    }

    /**
     * Вызвается при выполнении любого GET/POST запроса, который должен учитываться
     */
    public function onRequest()
    {
        if($this->isCurrentControllerExcluded() || Utils::isRequestTrusted($this->config))
        {
            return;
        }
                       
        $allowed_interval       = $this->getAllowedRequestInterval();
        $trigger_request_count  = $this->getTriggerRequestCount();

        $ip     = $_SERVER['REMOTE_ADDR'];
        $obj    = new RequestCount();

        // текущее время в миллисекундах
        $milli_time = round(microtime(true) * 1000);

        // пограничное время в прошлом, запросы после которого сбрасывают счетчик
        $edge_time = $milli_time - $allowed_interval;

        // Выполнение запроса на вставку или обновление счетчика запросов
        $sql = "INSERT INTO {$obj->_getTable()} (`ip`,`last_time`,`count`)
                VALUES ('{$ip}', {$milli_time}, 0)
                ON DUPLICATE KEY UPDATE
                  `count` = IF(`last_time` > {$edge_time}, `count`+1, 0),
                  `last_time` = {$milli_time}";

        \RS\Db\Adapter::SQLExec($sql);

        // Если превышен лимит на количество частых запросов
        $obj = Request::make()->from($obj)->where(['ip'=>$ip])->object();

        if((int)($obj['count'] ?? 0) >= $trigger_request_count)
        {
            $this->onRequestCountLimitExceeded();

            // Обнуление счетчика запросов
            $obj['count'] = 0;
            $obj->update();
        }
    }


    public function onRequestCountLimitExceeded()
    {
        $server_array = \RS\Http\Request::commonInstance()->getSource(SERVER);
        $ip     = $server_array['REMOTE_ADDR'];

        // Вставка записи об атаке в таблицу events
        $event              = new Event();
        $event['dateof']    = date('Y-m-d H:i:s');
        $event['component'] = Event::COMPONENT_PROACTIVE;
        $event['type']      = Event::TYPE_PROBLEM;

        $info       = new RequestCountAttackInfo();
        $info->ip   = $ip;
        $info->url  = \RS\Http\Request::commonInstance()->getSelfUrl();
        $info->user_agent   = isset($server_array['HTTP_USER_AGENT']) ? $server_array['HTTP_USER_AGENT'] : '';

        $event->setDetails($info);
        $event->insert();

        // Если включено "Автоматически блокировать вредоносные запросы"
        if($this->config->proactive_auto_block)
        {
            // Блокировка пользователя
            $duration = (int)$this->config->proactive_block_duration;

            $blocked = new \Main\Model\Orm\BlockedIp();
            $blocked['ip'] = $ip;
            $blocked['expire'] = $duration ? date('Y-m-d H:i:s', time() + $duration) : null;
            $blocked['comment'] = t('Блокировка по частоте запросов');
            $blocked->replace();

            // Помечаем событие как "FIXED"
            $event['type']      = Event::TYPE_FIXED;
            $event->update();
        }
    }

    public function onCron()
    {
        // Удаление старых счетчиков

        $delete_interval = 300; // секунды

        Request::make()
            ->from(new RequestCount())
            ->where('`last_time` < ' . (time() - $delete_interval) * 1000)
            ->delete()
            ->exec();
    }

    /**
     * Является ли текущий контроллер исключением.
     * Запросы на исключенные контроллеры не считаются
     *
     * @return bool
     */
    private function isCurrentControllerExcluded()
    {
        $excluded_routes_id = ['main.admin']; //'photo.image', 'photo.stub', 'main.image'
        $route = \RS\Router\Manager::getCurrentRoute();

        $exclude_controllers_name = [
            '\RS\Img\Handler\AbstractHandler',
            '\Exchange\Controller\Front\Gate',
            '\MobileSiteApp\Controller\Front\Gate',
            '\SiteUpdate\Controller\Admin\Wizard'
        ];

        $current_controller = $route->getController();
        foreach($exclude_controllers_name as $controller_class) {
            if (is_subclass_of($current_controller, $controller_class)) {
                $excluded_routes_id[] = $route->getId();
            }
        }

        return in_array($route->getId(), $excluded_routes_id);
    }

}