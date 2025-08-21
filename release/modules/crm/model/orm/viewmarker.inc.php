<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use Crm\Config\ModuleRights;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use RS\Event\Manager as EventManager;

class ViewMarker extends OrmObject
{
    protected static
        $table = 'crm_view_marker';

    function _init()
    {
        parent::_init()->append([
            'user_id' => new Type\User([
                'description' => t('Пользователь')
            ]),
            'record_type' => new Type\Enum(['reset', 'view'], [
                'description' => t('Тип записи о просмотре'),
                'allowEmpty' => false,
            ]),
            'entity_type' => new Type\Varchar([
                'description' => t('Тип связанной сущности')
            ]),
            'entity_id' => new Type\Integer([
                'description' => t('ID связанной сущности'),
            ]),
            'last_date' => new Type\Datetime([
                'description' => t('Дата записи о просмотре')
            ]),
            'status' => new Type\Varchar([
                'description' => t('Статус объекта')
            ]),
        ]);
    }

    /**
     * Вызывается перед сохранением объекта
     *
     * @param string $save_flag - строковое представление текущей операции (insert или update)
     * @return false|void
     */
    function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG) {
            $this['last_date'] = date('Y-m-d H:i:s');
        }

        return null;
    }
}