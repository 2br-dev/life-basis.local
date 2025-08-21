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
class ChecklistGroup extends OrmObject
{
    protected static
        $table = 'crm_checklist_group';

    function _init()
    {
        parent::_init()->append([
            'task_id' => new Type\Integer([
                'description' => t('ID задачи'),
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название группы')
            ]),
            'uniq' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('Уникальный Идентификатор'),
                'visible' => false,
                'unique' => true
            ]),
        ]);
    }

    /**
     * Обработчик, вызывается сразу после загрузки объекта
     */
    public function afterObjectLoad()
    {
        $this['items'] = $this->getChecklistItems();
    }

    /**
     * Возвращает все связанные задачи
     */
    public function getChecklistItems()
    {
        return \RS\Orm\Request::make()
            ->from(new ChecklistItem())
            ->where(['group_id' => $this['id']])
            ->orderby('sortn')
            ->exec()->fetchSelected('uniq');
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