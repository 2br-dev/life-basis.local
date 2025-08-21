<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

use ExternalApi\Model\Orm\VirtualApp;
use RS\Cache\Manager;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;

/**
 * PHP API для работы с виртуальными приложениями
 */
class VirtualAppApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\VirtualApp(), [
            'nameField' => 'title'
        ]);
    }

    /**
     * Возвращает список включенных ORM-объектов Виртуальных приложений
     *
     * @param bool $cache
     * @return array
     */
    public static function getEnabledVirtualApps($cache = true)
    {
        if ($cache) {
            return Manager::obj()
                ->watchTables(new VirtualApp())
                ->request([__CLASS__, 'getEnabledVirtualApps'], false);
        } else {
            return Request::make()
                ->from(new VirtualApp())
                ->where(['enable' => 1])
                ->objects();
        }
    }
}