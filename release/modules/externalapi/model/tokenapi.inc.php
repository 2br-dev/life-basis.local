<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

use ExternalApi\Model\Orm\AuthorizationToken;
use RS\Config\Loader;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\OrmObject;
use RS\Orm\Request;

/**
* Работа с авторизационными токенами
*/
class TokenApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\AuthorizationToken(), [
            'idField' => 'token'
        ]);
    }

    /**
     * Создаёт новый токен для пользователя по его id и id клиентского модуля
     *
     * @param integer $user_id - id пользователя
     * @param integer $client_id - id клиентского приложения для которго создан токен
     * @return Orm\AuthorizationToken
     * @throws \RS\Exception
     */
    public static function createToken($user_id, $client_id)
    {
         $config = Loader::byModule('externalapi');
         $token = new AuthorizationToken();
            
         //Удаляем протухшие token'ы
         Request::make()
            ->delete()
            ->from($token)
            ->where("expire < '#time'", [
                'time' => time(),
            ])->exec();
        
         //Регистрируем новый token
         $token['user_id'] = $user_id;
         $token['app_type'] = $client_id;
         $token['ip'] = $_SERVER['REMOTE_ADDR'];
         $token['dateofcreate'] = date('c');
         $token['expire'] = time() + $config->token_lifetime;
         $token->insert();
         return $token;
    }

    /**
     * Возвращает существующий или создает токен для пользователя по его id и id клиентского модуля
     *
     * @param integer $user_id - id пользователя
     * @param integer $client_id - id клиентского приложения для которго создан токен
     * @param int $min_lifetime минимальный срок жизни токена в секундах. Если существующий токен истекает ранее, то он будет пересоздан
     */
    public static function getOrCreateToken($user_id, $client_id, $min_lifetime = 2592000)
    {
        $token = Request::make()
            ->from(new AuthorizationToken())
            ->where([
                'user_id' => $user_id,
                'app_type' => $client_id
            ])->object();

        if ($token) {
            if ($token['expire'] > time() + $min_lifetime) {
                return $token;
            }
            $token->delete();
        }

        return self::createToken($user_id, $client_id);
    }

    /**
     * Удаляет авторизационный токен пользователя
     *
     * @param integer $user_id
     * @param string $client_id
     */
    public static function removeToken($user_id, $client_id)
    {
        Request::make()
            ->delete()
            ->from(new AuthorizationToken())
            ->where([
                'user_id' => $user_id,
                'app_type' => $client_id
            ])->exec();
    }
}
