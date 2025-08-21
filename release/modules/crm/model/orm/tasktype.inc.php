<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use RS\Config\Loader;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Users\Model\Orm\User;

/**
 * ORM объект - "Тип задачи"
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Название типа задачи
 * @property integer $creator_user_id Создатель задачи
 * @property integer $implementer_user_id Исполнитель задачи
 * @property array $collaborator_users_id Соисполнители задачи
 * @property array $observer_users_id Наблюдатели задачи
 * --\--
 */
class TaskType extends OrmObject
{
    protected static $table = 'crm_task_type';

    protected function _init()
    {
        $config = Loader::byModule(__CLASS__);
        $router = \RS\Router\Manager::obj();

        parent::_init()->append([
            t('Основные'),
                'title' => new Type\Varchar([
                    'maxLength' => '150',
                    'description' => t('Название'),
                    'Checker' => ['chkEmpty', t('Необходимо заполнить поле название')],
                    'hint' => t('Придумайте название типа задачи')
                ]),
                'creator_user_id' => new Type\User([
                    'description' => t('Создатель задачи'),
                    'hint' => t('Этот пользователь будет установлен в качестве создателя сразу при создании задачи этого типа'),
                    'compare' => function($value, $property, $orm) {
                        $user = new User($value);
                        return $user->getFio()."($value)";
                    }
                ]),
                'implementer_user_id' => new Type\User([
                    'description' => t('Исполнитель задачи'),
                    'hint' => t('Этот пользователь будет установлен в качестве исполнителя сразу при создании задачи этого типа'),
                    'requestUrl' => $router->getAdminUrl('ajaxEmail', [
                        'groups' => $config->implementer_user_groups
                    ], 'users-ajaxlist'),
                    'compare' => function($value, $property, $orm) {
                        $user = new User($value);
                        return $user->getFio()."($value)";
                    }
                ]),
                'collaborator_users_id' => new Type\Users([
                    'description' => t('Соисполнители'),
                    'requestUrl' => $router->getAdminUrl('ajaxEmail', [
                        'groups' => $config->implementer_user_groups
                    ], 'users-ajaxlist'),
                    'hint' => t('Эти пользователи будут установлены в качестве соисполнителей сразу при создании задачи этого типа. Соисполнители будут иметь такой же доступ к задаче, как и исполнитель'),
                ]),
                'observer_users_id' => new Type\Users([
                    'description' => t('Наблюдатели'),
                    'requestUrl' => $router->getAdminUrl('ajaxEmail', [
                        'groups' => $config->observer_user_groups
                    ], 'users-ajaxlist'),
                    'hint' => t('Эти пользователи будут установлены в качестве наблюдателей сразу при создании задачи этого типа. Наблюдатели будут получать уведомления об изменениях в задаче'),
                ]),
        ]);
    }

    /**
     * Возвращает идентификатор в менеджере связей
     *
     * @return string
     */
    public static function getLinkSourceType()
    {
        return 'task_type';
    }

    /**
     * Обработчик сохранения объекта
     *
     * @param string $flag
     */
    public function afterWrite($flag)
    {
        if ($this->isModified('collaborator_users_id')) {
            UserLink::saveUserLinks($this->getLinkSourceType(), $this['id'], $this['collaborator_users_id'], UserLink::USER_ROLE_COLLABORATOR);
        }

        if ($this->isModified('observer_users_id')) {
            UserLink::saveUserLinks($this->getLinkSourceType(), $this['id'], $this['observer_users_id'], UserLink::USER_ROLE_OBSERVER);
        }
    }

    /**
     * Обработчик, вызывается сразу после загрузки объекта
     */
    public function afterObjectLoad()
    {
        $this['collaborator_users_id'] = UserLink::getUserLinks($this->getLinkSourceType(), $this['id'], UserLink::USER_ROLE_COLLABORATOR);
        $this['observer_users_id'] = UserLink::getUserLinks($this->getLinkSourceType(), $this['id'], UserLink::USER_ROLE_OBSERVER);
    }
}
