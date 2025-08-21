<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Config;

use RS\AccessControl\AutoCheckers\ControllerChecker;
use \RS\AccessControl\Right;
use RS\Cache\Manager as CacheManager;
use Users\Model\GroupApi;

class ModuleRights extends \RS\AccessControl\DefaultModuleRights
{
    const RIGHT_LOGIN_AS_USER = 'login_as_user';
    const RIGHT_VERIFICATION_SESSION = 'verification_session';
    const RIGHT_SET_USERS_TO_GROUP_PREFIX = 'set_users_to_group_';

    protected function getSelfModuleRights()
    {
        $rights = [
            new Right(self::RIGHT_READ, t('Чтение')),
            new Right(self::RIGHT_CREATE, t('Создание')),
            new Right(self::RIGHT_UPDATE, t('Изменение')),
            new Right(self::RIGHT_DELETE, t('Удаление')),
            new Right(self::RIGHT_LOGIN_AS_USER, t('Авторизация под пользователем')),
            new Right(self::RIGHT_VERIFICATION_SESSION, t('Управление сессиями верификации')),
        ];

        if (\Setup::$INSTALLED && $groups = GroupApi::getRealUserGroups()) {
            foreach ($groups as $alias => $group) {
                $rights[] = new Right(self::RIGHT_SET_USERS_TO_GROUP_PREFIX . $alias, t('Добавление пользователя в группу "%0"', $group));
            }
        }

        return $rights;
    }

    /**
     * Возвращает собственные инструкции для автоматических проверок
     *
     * @return \RS\AccessControl\AutoCheckers\AutoCheckerInterface[]
     */
    protected function getSelfAutoCheckers()
    {
        return array_merge(parent::getSelfAutoCheckers(), [
            new ControllerChecker('users-admin-verificationctrl', '*', '*', [], self::RIGHT_VERIFICATION_SESSION),
        ]);
    }
}