<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Oauth;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\TokenApi;
use RS\Exception;
use RS\RemoteApp\Manager as RemoteAppManager;
use RS\Site\Manager as SiteManager;
use Users\Model\AuthorizationKey;

/**
 * Метод API для авторизации через QR-код с другого устройства, где клиент уже авторизован
 */
class QrCode extends AbstractMethod
{
    /**
     * Возвращает какими методами могут быть переданы параметры для данного метода API
     *
     * @return array
     */
    public function getAcceptRequestMethod()
    {
        return [POST, JSON];
    }

    /**
     * Авторизовывает пользователя по ключу из QR-кода
     * ---
     * Метод поддерживает прием параметров только методом POST.
     *
     * @param string $client_id Уникальный идентификатор приложения, которое запрашивает авторизацию пользователя
     * @param string $client_secret Секретный ключ приложения, которое запрашивает авторизацию пользователя
     * @param string $key Авторизационный ключ, полученный из QR-кода (из параметра key)
     *
     * @example POST /api/methods/oauth.qrCode?client_id=app&client_secret=XXXXXXX&key=eyJ1c2VyIjoiaDZvenJoOWR0a3JrZGptIiwicmFuZCI6InNWZHk2UmxraHoiLCJle.ec135f4e1a183a89aeded872e75fc2425f306bc...
     *
     * Ответ:
     * <pre>
     * {
     *      'response': {
     *            'auth': {
     *                'token' => '38b83885448a8ad9e2fb4f789ec6b0b690d50041',
     *                'expire' => '1504785044',
     *            },
     *            'user': {
     *                "id": "1",
     *                "name": "Супервизор тест тест",
     *                "surname": " Моя фамилия",
     *                "midname": " ",
     *                "e_mail": "admin3@admin.ru",
     *                "login": "admin3@admin.ru",
     *                "phone": "+7(xxx)xxx-xx-xx",
     *                "sex": "",
     *                "subscribe_on": "0",
     *                "dateofreg": "2016-03-14 19:58:58",
     *                "ban_expire": null,
     *                "last_visit": "2016-11-09 15:29:14",
     *                "is_company": "0",
     *                "company": "",
     *                "company_inn": "",
     *                "data": [],
     *                "push_lock": null,
     *                "user_cost": null,
     *                "birthday": null,
     *                "fio": "Моя фамилия Супервизор тест тест",
     *                "groups": [
     *                    "guest",
     *                    "clients",
     *                    "supervisor"
     *                ],
     *                "is_courier": false
     *            },
     *            'site_uid' : "38b83885448a8ad9e2fb4f789ec6b0b690d50041"
     *        }
     * }
     * </pre>
     *
     * @return array Возвращает информацию об авторизованном пользователе или ошибку
     */
    public function process($client_id, $client_secret, $key)
    {
        $app = $this->checkApp($client_id, $client_secret);
        $site_uid = SiteManager::getSite()->getSiteHash();
        $response = [
            'response' => [
                'site_uid' => $site_uid
            ]
        ];

        try {
            $auth_key = AuthorizationKey::makeByKey($key);
            $user = $auth_key->getUser();
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_BAD_AUTHORIZATION);
        }

        //Проверяем группу пользователя, соответствует ли она требованиям приложения
        if (!array_intersect($app->getAllowUserGroup(), $user->getUserGroups())) {
            throw new ApiException(t('Пользователь не имеет права доступа к приложению'), ApiException::ERROR_APP_ACCESS_DENIED);
        }

        $token = TokenApi::createToken($user['id'], $client_id);
        $token_data = Login::makeResponseAuthTokenData($token);
        $auth_user = Login::makeResponseUserData($user);

        $response['response'] += [
            'auth' => $token_data,
            'user' => $auth_user
        ];

        return $response;
    }


    /**
     * Проверяет корректность параметров client_id и client_secret.
     *
     * @param string $client_id Уникальный идентификатор приложения, которое запрашивает авторизацию пользователя
     * @param string $client_secret Секретный ключ приложения, которое запрашивает авторизацию пользователя
     * @return \RS\RemoteApp\AbstractAppType Возвращает объект приложения
     * @throws ApiException
     */
    protected function checkApp($client_id, $client_secret)
    {
        $app = RemoteAppManager::getAppByType($client_id);

        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        return $app;
    }
}