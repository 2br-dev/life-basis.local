<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use ExternalApi\Model\TokenApi;
use RS\Application\Auth as AppAuth;
use RS\Config\Loader;
use Users\Model\Api as UserApi;
use Users\Model\Verification\VerificationEngine;

/**
* Изменение пароля
*/
class ChangePassword extends AbstractMethod
{
    protected $user_api;

    public function __construct()
    {
        parent::__construct();
        $this->user_api = new UserApi();
    }

    /**
     *  Изменяет пароль пользователя
     *
     * @param string $client_id ID клиентского приложения
     * @param string $new_pass Пароль
     * @param string $new_pass_confirm Повтор пароля
     * @param string $uniq Уникальный код пользователя. Обязателен, если не передан параметр session_token.
     * @param string $session_token Токен верификационной сессии. Обязателен, если не передан параметр uniq.
     *
     * @return array Возвращает информацию об авторизованном пользователе или ошибку
     * @throws \RS\Exception
     * @example POST /api/methods/user.changePassword?client_id=myapp&session_token=123123123123123&new_pass=123123&new_pass_confirm=123123
     *
     * В случае успешной регистрации:
     *
     * <pre>
     *   {
     *       "response": {
     *           "success": true,
     *           "errors": '',
     *           "auth": {
     *               "token": "ff22086b9e8c2c2fad406e04265df1c5a8ddc124",
     *               "expire": 1678458003
     *           },
     *           "user": {
     *               "id": 9,
     *               "name": "Иван",
     *               "surname": "Иванов",
     *               "midname": "Иванович",
     *               "e_mail": "admin@admin.ru",
     *               "login": null,
     *               "phone": "880008008030",
     *               "sex": null,
     *               "subscribe_on": null,
     *               "dateofreg": "2022-03-10 17:20:03",
     *               "ban_expire": null,
     *               "last_visit": null,
     *               "last_ip": null,
     *               "is_enable_two_factor": null,
     *               "is_company": null,
     *               "company": null,
     *               "company_inn": null,
     *               "data": [],
     *               "desktop_notice_locks": null,
     *               "user_cost": null,
     *               "allow_api_methods": null,
     *               "push_lock": null,
     *               "manager_user_id": null,
     *               "basket_min_limit": null,
     *               "source_id": null,
     *               "date_arrive": null,
     *               "fio": "Иванов Иван Иванович",
     *               "groups": [
     *                   "guest",
     *                   "clients"
     *               ]
     *           }
     *       }
     *   }</pre>
     *
     * В случае ошибки:
     *
     *  <pre>
     *  {
     *      "response": {
     *          "success": false,
     *          "errors": "Повтор пароля не соответствует паролю "
     *      }
     *  }</pre>
     *
     */
    protected function process($client_id, $new_pass, $new_pass_confirm, $uniq = null, $session_token = null, $token = null)
    {
        $response = [
            'response' => [
                'success' => false,
                'errors' => '',
            ]
        ];
        $user = null;

        if (!$uniq && !$session_token) {
            throw new ApiException(t('Передан неверный набор параметров. Не найдены параметры uniq или session_token'), ApiException::ERROR_WRONG_PARAMS);
        }

        if ($uniq) {
            $user = $this->user_api->getByHash($uniq);
        }elseif ($session_token) {
            $verification_engine = new VerificationEngine();
            if (!$verification_engine->initializeByToken($session_token)) {
                throw new ApiException(t('Верификационная сессия с таким токеном не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
            }
            $session = $verification_engine->getSession();

            if (!$session->isResolved()) {
                throw new ApiException(t('Верификационная сессия истекла. Обновите страницу'), ApiException::INTERNAL_ERROR_ID);
            }

            $user = $session->getCreatorUser();
        }

        if (!$user) {
            $response['response']['errors'] = t('Пользователь не найден');
        }else {
            if ($this->user_api->changeUserPassword($user, $new_pass, $new_pass_confirm)) {
                //Авторизовываем пользователя
                $config = Loader::byModule('users');
                if ($config->fieldIsLogin('login') && $user['login'] !== '') {
                    $login = $user['login'];
                } elseif ($config->fieldIsLogin('e_mail') && $user['e_mail'] !== '') {
                    $login = $user['e_mail'];
                } else {
                    $login = $user['phone'];
                }

                if (AppAuth::login($login, $new_pass)) {
                    $current_user = AppAuth::getCurrentUser();

                    $token = TokenApi::createToken($current_user['id'], $client_id);
                    $token_data = Login::makeResponseAuthTokenData($token);
                    $auth_user = Login::makeResponseUserData($current_user);

                    //Не передаем пароль в ответе
                    unset($auth_user['openpass'], $auth_user['openpass_confirm']);

                    $response['response'] += [
                        'auth' => $token_data,
                        'user' => $auth_user
                    ];
                    $response['response']['success'] = true;
                }else {
                    $response['response']['errors'] = [AppAuth::getError()];
                }
            } else {
                $response['response']['errors'] = $this->user_api->getErrorsStr();
            }
        }

        return $response;
    }
}