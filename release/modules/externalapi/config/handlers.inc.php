<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Config;

use ExternalApi\Model\App\VirtualAppInstance;
use ExternalApi\Model\Behavior\UsersUser;
use ExternalApi\Model\Orm\UserApiMethodAccess;
use ExternalApi\Model\Router\ApiRoute;
use ExternalApi\Model\VirtualAppApi;
use RS\Orm\Request;
use Users\Model\Orm\User;
use RS\Orm\Type;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('initialize')
            ->bind('getroute')
            ->bind('getapps')
            ->bind('orm.init.users-user')
            ->bind('orm.afterwrite.users-user')
            ->bind('orm.afterdelete.users-user')
            ->bind('externalapi.getexceptions');
    }

    /**
     * Расширяет поведение объекта Пользователь
     */
    public static function initialize()
    {
        \Users\Model\Orm\User::attachClassBehavior(new UsersUser());
    }

    /**
     * Регистрирует маршруты модуля в системе
     *
     * @param $routes
     * @return array
     */
    public static function getRoute($routes)
    {
        $routes[] = new ApiRoute('externalapi-front-apigate', [
            "/api-{api_key}/methods/{method}",
            "/api/methods/{method}",
        ], null, t('Шлюз обмена данными по API'), true);

        $routes[] = new ApiRoute('externalapi-front-apigate-help', [
            "/api-{api_key}/help/{method}",
            "/api-{api_key}/help",

            "/api/help/{method}",
            "/api/help",
        ], [
            'controller' => 'externalapi-front-apigate',
            'Act' => 'help'
        ], t('Описание методов API'), true);
        
        return $routes;
    }


    /**
     * Добавляет к пользователю возможность настроить разрешенные методы API, требующие авторизации
     */
    public static function ormInitUsersUser(User $user)
    {
        $user->getPropertyIterator()->append([
            t('Внешнее API'),
            'allow_api_methods' => new Type\ArrayList([
                'description' => t('Разрешенные авторизованные методы API'),
                'hint' => t('Методы API, поддерживающие передачу авторизационного токена могут быть включены для выборочных пользователей. Данные настройки будут перекрывать настройки модуля Внешнее API'),
                'list' => [['ExternalApi\Model\ApiRouter', 'getAuthorizedApiMethodsSelectList'], ['all' => 'Все']],
                'appVisible' => false,
                'checkboxListView' => true,
                'sites' => \RS\Site\Manager::getSiteList(),
                'template' => '%externalapi%/form/user/allow_api_methods.tpl'
            ])
        ]);
    }
    
    /**
    * Удаляет все token'ы, выданные пользователю, если тот сменил пароль
    * 
    * @param array $param
    */
    public static function ormAfterwriteUsersUser($param)
    {
        $user = $param['orm'];
        if ($user->isModified('pass')) {
            
            \RS\Orm\Request::make()
                ->delete()
                ->from(new \ExternalApi\Model\Orm\AuthorizationToken())
                ->where([
                    'user_id' => $user['id']
                ])
                ->exec();
        }

        //Сохраняем доступные методы API для пользователя
        if ($user->isModified('allow_api_methods')) {
            Request::make()
                ->delete()
                ->from(new UserApiMethodAccess())
                ->where([
                    'user_id' => $user['id']
                ])->exec();

            foreach($user['allow_api_methods'] as $site_id => $methods) {
                foreach($methods as $method) {
                    $user_access = new UserApiMethodAccess();
                    $user_access['site_id'] = $site_id;
                    $user_access['user_id'] = $user['id'];
                    $user_access['api_method'] = $method;
                    $user_access->insert();
                }
            }
        }
    }
    
    /**
    * Возвращаем классы исключений, которые используются в методах API
    * 
    * @param \ExternalApi\Model\AbstractException[] $list
    * @return \ExternalApi\Model\AbstractException[]
    */
    public static function externalApiGetExceptions($list)
    {
        $list[] = new \ExternalApi\Model\Exception();
        return $list;
    }

    /**
     * Вызывается после удаления пользователя.
     * Удаляет все атворизационные токены при удалении пользователя
     *
     * @param $param
     */
    public static function ormAfterDeleteUsersUser($param)
    {
        $user = $param['orm'];
        \RS\Orm\Request::make()
            ->delete()
            ->from(new \ExternalApi\Model\Orm\AuthorizationToken())
            ->where([
                'user_id' => $user['id']
            ])
            ->exec();
    }

    /**
     * Регистрирует виртуальные приложения в системе
     *
     * @return array
     */
    public static function getapps($list)
    {
        $virtual_apps_orm = VirtualAppApi::getEnabledVirtualApps();
        foreach($virtual_apps_orm as $app) {
            $list[] = new VirtualAppInstance($app);
        }
        return $list;
    }
}
