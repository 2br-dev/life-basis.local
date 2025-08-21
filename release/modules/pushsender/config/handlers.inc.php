<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Config;
use \RS\Orm\Type;
use \PushSender\Model;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('api.oauth.token.success')
            ->bind('orm.init.users-user')
            ->bind('orm.afterwrite.users-user')
            ->bind('getlogs')
            ->bind('getmenus');
    }

    /**
     * Регистрируем Лог для Push уведомлений
     *
     * @param $list
     * @return mixed
     */
    public static function getLogs($list)
    {
        $list[] = Model\Log\PushSenderLog::getInstance();
        return $list;
    }

    /**
     * Сохраняем Firebase токены, если они были сообщены при авторизации
     * через oauth.token мобильным приложением. Токен передается в параметре custom[push_token]=.....
     *
     * @param array $params - параметры удачной авторизации
     * @throws \RS\Orm\Exception
     */
    public static function ApiOauthTokenSuccess($params)
    {
        if (isset($params['params']['custom']['push_token'])) {
            
            $user_id    = $params['result']['response']['user']['id'];
            $push_token = $params['params']['custom']['push_token'];
            $app        = $params['params']['client_id'];
            
            \PushSender\Model\PushTokenApi::registerUserToken($push_token, $app, $user_id);
        }
    }

    /**
     * Добавляем возможность включать/выключать пользователям право отправлять push уведомления
     *
     * @param \Users\Model\Orm\User $orm_user - объект пользователя
     * @throws \RS\Db\Exception
     */
    public static function ormInitUsersUser(\Users\Model\Orm\User $orm_user)
    {        
        $orm_user->getPropertyIterator()->append([
            t('Push-уведомления'),
                'push_lock' => new Type\ArrayList([
                    'description' => t('Запретить получение следующих push уведомлений'),
                    'template' => '%pushsender%/admin/user_push_lock.tpl',
                    'lockApi' => new Model\PushLockApi(),
                    'sites' => \RS\Site\Manager::getSiteList(),
                    'meVisible' => false,
                ])
        ]);
    }

    /**
     * Сохраняем сведения о запретах на Push уведомления
     *
     * @param array $param - массив параметров
     */
    public static function ormAfterwriteUsersUser($param)
    {
        $user = $param['orm'];
        
        if ($user->isModified('push_lock')) {
            \RS\Orm\Request::make()
                ->delete()
                ->from(new \PushSender\Model\Orm\PushLock())
                ->where([
                    'user_id' => $user['id']
                ])->exec();
            
            foreach($user['push_lock'] as $site_id => $data) {
                foreach($data as $app => $notices) {
                    foreach($notices as $notice) {
                        $lock               = new \PushSender\Model\Orm\PushLock();
                        $lock['site_id']    = $site_id;
                        $lock['user_id']    = $user['id'];
                        $lock['app']        = $app;
                        $lock['push_class'] = $notice;
                        $lock->insert();
                    }
                }
            }
        }
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items - массив пунктов меню установленных ранее
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Push токены'),
                'alias' => 'pushtokens',
                'link' => '%ADMINPATH%/pushsender-pushtokenctrl/',
                'typelink' => 'link',
                'sortn' => 2,              
                'parent' => 'modules'
        ];
        return $items;
    }
}
