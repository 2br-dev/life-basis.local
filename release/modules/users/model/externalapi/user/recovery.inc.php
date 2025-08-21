<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use RS\Application\Auth as AppAuth;
use Users\Model\Api as UserApi;
use Users\Model\Verification\Action\TwoStepRecoverPassword;
use Users\Model\Verification\VerificationEngine;

/**
* Инициализация восстановления пароля пользователя
*/
class Recovery extends AbstractMethod
{
    protected $user_api;

    public function __construct()
    {
        parent::__construct();
        $this->user_api = new UserApi();
    }

    /**
     * Запрос на восстановление пароля (с поддержкой верификационных сессий)
     *
     * @return array Возвращает верификационную сессию, информационный текст или ошибку
     * @throws \RS\Exception
     * @example POST /api/methods/user.recovery?login=+71234567980
     *
     * Ответ зависит он настроек.
     * Если в настройках включена возможность восстановления пароля по телефону, и введен номер телефона для восстановления,
     * то будет запущена верификационная сессия, и информации о ней будет передана в ответе, пример:
     * <pre>
     * "response": {
     *      "success": true,
     *      "type": "phone",
     *      "error": "",
     *      "text": "",
     *      "verification": {
     *          "session": {
     *          "token": "123123123123132",
     *          "error": "",
     *          "code_send_flag": false,
     *          "code_refresh_delay": 0,
     *          "is_resolved": true,
     *          "code_debug": "1234"
     *          }
     *      }
     * }
     * </pre>
     * Если будет введен номер email, то инструкция по восстановлению пароля будет отправлена на E-mail,
     * в ответе будет передан информационный текст:
     *
     * <pre>
     * "response": {
     *      "success": true,
     *      "type": "email",
     *      "error": "",
     *      "text": "На указанный E-mail будет отправлено письмо с дальнейшими инструкциями по восстановлению пароля"
     * }
     * </pre>
     * В случае ошибки:
     *
     * <pre>
     * "response": {
     *      "success": false,
     *      "type": "",
     *      "error": "Пользователь не найден",
     *      "text": ""
     * }
     * </pre>
     *
     */
    protected function process($login)
    {
        $response = [
            'response' => [
                'success' => false,
                'type' => '',
                'error' => '',
                'text' => ''
            ]
        ];
        $user = AppAuth::getUserByLogin($login);

        if ($user) {
            $recover_type = $this->user_api->getRecoverTypeByLogin($login, $user);
            $response['response']['type'] = $recover_type;

            if ($recover_type == UserApi::RECOVER_TYPE_PHONE) {
                $verification_engine = (new VerificationEngine())
                    ->setCreatorUserId($user['id'])
                    ->setAction(new TwoStepRecoverPassword())
                    ->setPhone($user['phone']);

                $verification_engine->initializeSession();

                $response['response']['success'] = true;
                $response['response']['verification'] = [
                    'session' => Login::makeResponseVerificationSessionData($verification_engine->getSession())
                ];
            }elseif ($recover_type == UserApi::RECOVER_TYPE_EMAIL) {
                $success = $this->user_api->sendRecoverEmailByUser($user);

                if (!$success) {
                    $response['response']['error'] = $this->user_api->getErrorsStr();
                }else {
                    $response['response']['success'] = true;
                    $response['response']['text'] = t('На указанный E-mail будет отправлено письмо с дальнейшими инструкциями по восстановлению пароля');
                }
            }else {
                $empty_field = $recover_type == UserApi::RECOVER_TYPE_NONE_EMAIL ? 'e-mail' : t('телефон');
                $response['response']['error'] = t('У пользователя не заполнен %0', [$empty_field]);
            }
        }else {
            $response['response']['error'] = t('Пользователь не найден');
        }

        return $response;
    }
}