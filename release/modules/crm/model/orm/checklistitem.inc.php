<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;
use RS\Orm\OrmObject;
use \RS\Orm\Type;

/**
 * ORM объект - связь между объектами
 * --/--
 * @property integer $task_id ID задачи
 * @property string $title Название группы
 * --\--
 */
class ChecklistItem extends OrmObject
{
    protected static
        $table = 'crm_checklist_item';

    function _init()
    {
        parent::_init()->append([
            'group_id' => new Type\Integer([
                'description' => 'ID группы'
            ]),
            'title' => new Type\Varchar([
                'description' => 'Текст задачи'
            ]),
            'uniq' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('Уникальный Идентификатор'),
                'visible' => false,
                'unique' => true
            ]),
            'entity_type' => new Type\Varchar([
                'description' => t('Тип связанной сущности')
            ]),
            'entity_id' => new Type\Integer([
                'description' => t('ID связанной сущности'),
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Сортировочный индекс'),
                'visible' => false
            ]),
            'is_done' => new Type\Integer([
                'description' => 'Задача выполнена',
                'default' => 0
            ]),
        ]);
    }

    /**
     * Загружает текущий объект по уникальному идентификатору
     *
     * @param string $uniq Уникальный идентификатор
     * @return self
     */
    public static function loadByUniq($uniq)
    {
        return self::loadByWhere([
            'uniq' => $uniq
        ]);
    }
}