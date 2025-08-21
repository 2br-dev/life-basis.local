<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM объект, характеризующий связь пользователей с задачей, сделкой
 * --/--
 * @property string $source_type Тип объекта связи
 * @property integer $source_id ID объекта связи
 * @property integer $user_id Пользователь
 * @property string $user_role Роль пользователя
 * --\--
 */
class UserLink extends AbstractObject
{
    const SOURCE_TYPE_TASK = 'task';
    const SOURCE_TYPE_TASK_TYPE = 'task_type';
    const SOURCE_TYPE_DEAL = 'deal';

    const USER_ROLE_COLLABORATOR = 'collaborator';
    const USER_ROLE_OBSERVER = 'observer';
    const USER_ROLE_IMPLEMENTER = 'implementer';

    protected static $table = 'crm_userlink';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'source_type' => new Type\Enum(array_keys(self::getSourceTypeTitles()), [
                'description' => t('Тип объекта связи'),
            ]),
            'source_id' => new Type\Integer([
                'description' => t('ID объекта связи'),
            ]),
            'user_id' => new Type\User([
                'description' => t('Пользователь')
            ]),
            'user_role' => new Type\Enum(array_keys(self::getUserRoleTitles()), [
                'description' => t('Роль пользователя')
            ])
        ]);

        $this->addIndex(['source_type', 'source_id', 'user_id', 'user_role'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает список возможных значений свойства source_type
     *
     * @return array
     */
    public static function getSourceTypeTitles()
    {
        return [
            self::SOURCE_TYPE_TASK => t('Задача'),
            self::SOURCE_TYPE_TASK_TYPE => t('Тип задачи'),
            self::SOURCE_TYPE_DEAL => t('Сделка'),
        ];
    }

    /**
     * Возвращает список возможных значений свойства user_role
     *
     * @return array
     */
    public static function getUserRoleTitles()
    {
        return [
            self::USER_ROLE_COLLABORATOR => t('Соисполнитель'),
            self::USER_ROLE_OBSERVER => t('Наблюдатель'),
            self::USER_ROLE_IMPLEMENTER => t('Исполнитель'),
        ];
    }

    /**
     * Сохраняет связь пользователей с объектами
     *
     * @param string $source_type
     * @param string $source_id
     * @param array $user_ids
     * @param string $user_role
     * @return bool
     */
    public static function saveUserLinks($source_type, $source_id, array $user_ids, $user_role)
    {
        Request::make()
            ->delete()
            ->from(new self())
            ->where([
                'source_type' => $source_type,
                'source_id' => $source_id,
                'user_role' => $user_role
            ])->exec();

        foreach($user_ids as $user_id) {
            $user_link = new self();
            $user_link['source_type'] = $source_type;
            $user_link['source_id'] = $source_id;
            $user_link['user_id'] = $user_id;
            $user_link['user_role'] = $user_role;
            $user_link->insert();
        }

        return true;
    }

    /**
     * Возвращаяет связь пользователей с объектами
     *
     * @param string $source_type
     * @param string $source_id
     * @param string $user_role
     * @return array
     */
    public static function getUserLinks($source_type, $source_id, $user_role)
    {
        return Request::make()
            ->from(new self())
            ->where([
                'source_type' => $source_type,
                'source_id' => $source_id,
                'user_role' => $user_role
            ])
            ->exec()
            ->fetchSelected(null, 'user_id');
    }
}