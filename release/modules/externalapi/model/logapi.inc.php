<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

use ExternalApi\Model\Orm\Log;
use RS\Config\Loader;
use RS\Db\Adapter;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

class LogApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Log());
    }

    /**
     * Сохраняет запись в журнале обращений к API
     *
     * @param \RS\Http\Request $url
     * @param string $method имя метода API
     * @param array $params параметры API
     * @param array $result ответ сервера
     * @param float $execution_time время выполнения запроса
     * @return Orm\Log|false
     */
    public static function writeToLog(\RS\Http\Request $url, $method, $params, $result, $execution_time)
    {
        if (Loader::byModule(__CLASS__)->enable_request_log) {
            //Очистим старые запросы, которые старше двух месяцев
            Request::make()
                ->delete()
                ->from(new Log())
                ->where("dateof < '".date('Y-m-d H:i:s', strtotime("-2 months"))."'")
                ->exec();

            $log = new Orm\Log();
            if (isset($params['token'])) {
                $token = new Orm\AuthorizationToken($params['token']);
                if ($token['token']) {
                    $log['user_id'] = $token['user_id'];
                    $log['token'] = $token['token'];
                    $log['client_id'] = $token['app_type'];
                }
            }
            
            $log['dateof'] = date('c');
            $log['ip'] = $_SERVER['REMOTE_ADDR'];
            $log['method'] = $method;
            $log['request_uri'] = $url->server('REQUEST_URI');
            $log['request_params'] = serialize($params);
            $log['execution_time'] = $execution_time;
            $saved_result = $result;
            if (is_array($saved_result)) {
                array_walk_recursive($saved_result, function (&$value) {
                    if (is_string($value)) {
                        $max_length = 1000;
                        if (mb_strlen($value) > $max_length) {
                            $value = mb_substr($value, 0, $max_length) . '...';
                        }
                    }
                });
            }

            $log['response'] = serialize($saved_result);
            if (is_array($result) && isset($result['error'])) {
                $log['error_code'] = $result['error']['code'];
            }
            $log->insert();
            return $log;
        }
        return false;
    }

    /**
     * Очистка лога
     */
    function clearLog()
    {
        $log = new Orm\Log();
        Adapter::sqlExec('TRUNCATE '.$log->_getTable());
    }
}