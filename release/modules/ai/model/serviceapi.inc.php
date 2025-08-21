<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use Ai\Config\File;
use Ai\Model\Orm\Service;
use Ai\Model\ServiceType\AbstractServiceType;
use Ai\Model\ServiceType\Gpt\ReadyScript;
use RS\Event\Manager as EventManager;
use RS\Module\AbstractModel\EntityList;

/**
 * API для работы со профилями GPT-сервисов
 */
class ServiceApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Service(), [
            'nameField' => 'title'
        ]);
    }

    /**
     * Возвращает список сервисов с которыми работает данный модуль
     *
     * @return array
     */
    public static function getServiceTypes()
    {
        $list = [
            ReadyScript::getId() => ReadyScript::class,
        ];
        $event_result = EventManager::fire('ai.getServiceTypes', []);
        foreach($event_result->getResult() as $id => $item) {
            $list[$id] = $item;
        }

        return $list;
    }

    /**
     * Возвращает список названий сервисов с которыми работает данный модуль
     *
     * @param array $first первый элемент списка
     * @param bool $except_readyscript Если true, то провайдер ReadyScript будет исключен из списка
     *
     * @return array
     */
    public static function getServiceTypeTitles(array $first = [], $except_readyscript = false)
    {
        $services = self::getServiceTypes();
        if ($except_readyscript) {
            unset($services[ReadyScript::getId()]);
        }

        $result = [];
        foreach($services as $id => $item) {
            $result[$id] = $item::getTitle();
        }
        return $first + $result;
    }

    /**
     * Возвращает объект GPT-сервиса по ID
     *
     * @param string $service_type_id
     * @param Service $service
     * @return AbstractServiceType | null
     */
    public static function getServiceTypeById($service_type_id, Service $service)
    {
        foreach(self::getServiceTypes() as $id => $item) {
            if ($id == $service_type_id) return new $item($service);
        }
        return null;
    }

    /**
     * Возвращает объект сервиса по умолчанию.
     *
     * @return Service
     */
    public static function getDefaultService()
    {
        return self::getServiceById(File::config()['default_service']);
    }

    /**
     * Возвращает объект сервиса по умолчанию для чата в админке.
     *
     * @return Service
     */
    public static function getDefaultChatService()
    {
        return self::getServiceById(File::config()['default_chat_service']);
    }

    /**
     * Возвращает GPT-сервис ReadyScript. Он всегда присутствует в системе "виртуально".
     *
     * @return Service
     */
    public static function getReadyScriptService()
    {
        $service = new Service();
        $service['id'] = Service::SERVICE_READYSCRIPT_ID;
        $service['title'] = 'ReadyScript';
        $service['type'] = ReadyScript::getId();
        $service['settings'] = [];

        return $service;
    }

    /**
     * Возвращает сервис по ID, включая сервис ReadyScript
     *
     * @return Service
     */
    public static function getServiceById($id)
    {
        if ($id == Service::SERVICE_READYSCRIPT_ID) {
            return self::getReadyScriptService();
        }

        if ($id == 0) {
            return self::getDefaultService();
        }

        return new Service($id);
    }
}