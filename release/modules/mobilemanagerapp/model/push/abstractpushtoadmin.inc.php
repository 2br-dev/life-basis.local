<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;

use PushSender\Model\Firebase\Push\RsPushNotice;
use RS\Config\Loader;
use RS\Orm\Request;
use Users\Model\Orm\UserInGroup;

/**
 * Абстрактный базовый класс для уведомлений для администратора
 */
abstract class AbstractPushToAdmin extends RsPushNotice
{
    /**
     * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
     *
     * @return string
     */
    public function getAppId()
    {
        return 'store-management';
    }

    /**
     * Возвращает одного или нескольких получателей - администраторов
     *
     * @return array
     */
    public function getRecipientUserIds()
    {
        $admin_groups = (array)Loader::byModule($this)->allow_user_groups;
        $shop_config = Loader::byModule('shop');
        $couriers_group = (array)($shop_config ? $shop_config->courier_user_group : []);
        $managers_group = (array)($shop_config ? $shop_config->manager_group : []);

        $real_admin_groups = array_diff($admin_groups, $couriers_group, $managers_group);
        if ($real_admin_groups) {
            $user_ids = Request::make()
                ->select('user')
                ->from(new UserInGroup())
                ->whereIn('group', $real_admin_groups)
                ->exec()->fetchSelected(null, 'user');

            return $user_ids;
        }
        return [];
    }
}