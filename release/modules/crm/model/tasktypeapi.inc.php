<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\FilterType\Collaborators;
use Crm\Model\FilterType\Observers;
use Crm\Model\Orm\UserLink;
use RS\Module\AbstractModel\EntityList;
use \RS\Html\Filter;

/**
 * Класс для организации выборок ORM объекта
 */
class TaskTypeApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\TaskType(), [
            'defaultOrder' => 'id DESC',
            'nameField' => 'title'
        ]);
    }

    /**
     * Возвращает структуру фильтра для фильтрации задач
     *
     * @return Filter\Control
     */
    public function getFilterControl()
    {
        return new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                        new Filter\Type\User('creator_user_id', t('Создатель')),
                        new Collaborators('implementer_user_id', UserLink::SOURCE_TYPE_TASK, t('Исполнитель')),
                        new Observers('observer_user_id', UserLink::SOURCE_TYPE_TASK, t('Наблюдатель')),
                    ]
                    ])
                ],
            ]),
            'Caption' => t('Поиск по типам')
        ]);
    }
}
