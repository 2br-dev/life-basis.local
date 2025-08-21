<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Orm;
use RS\Orm\OrmObject;
use Alerts\Model\Manager as AlertManager;
use RS\Config\Loader as ConfigLoader;
use RS\Orm\Type as OrmType;
use Users\Model\Notice\UserDeleteAdmin as NoticeUserDeleteAdmin;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id ID пользователя
 * @property string $save_date Дата создания
 * --\--
 */
class DeleteProfileRequests extends OrmObject
{
    protected static $table = 'user_delete_profile_requests';

    protected function _init()
    {
        parent::_init()->append([
            'site_id' => new OrmType\CurrentSite(),
            'user_id' => (new OrmType\Integer())
                ->setDescription(t('ID пользователя'))
                ->setAllowEmpty(false),
            'save_date' => (new OrmType\Datetime())
                ->setDescription('Дата создания')
        ]);

        $this->addIndex(['user_id'], self::INDEX_UNIQUE);
    }

    /**
     * Вызывается перед сохранением объекта
     *
     * @param string $flag - строковое представление текущей операции (insert или update)
     * @return false|void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['save_date'] = date('Y-m-d H:i:s');
        }
    }

    /**
     * Действия после записи объекта
     *
     * @param string $flag - insert или update
     */
    public function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG && \Setup::$INSTALLED) {

            $site_config = ConfigLoader::getSiteConfig();
            if ($site_config['admin_email']) {
                // Уведомление администратору
                $notice = new NoticeUserDeleteAdmin;
                $user = $this->getUser();
                $notice->init($user);
                AlertManager::send($notice);
            }
        }
    }

    /**
     * Возвращает пользователя
     *
     * @return User
     */
    public function getUser(): User
    {
        static $users = [];
        if (!isset($users[$this['user_id']])) {
            $users[$this['user_id']] = new User($this['user_id']);
        }
        return $users[$this['user_id']];
    }
}
