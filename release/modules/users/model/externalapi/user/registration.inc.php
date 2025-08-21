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
use RS\Application\Auth;
use RS\Application\Auth as AppAuth;
use RS\Config\Loader;
use RS\RemoteApp\Manager as RemoteAppManager;
use Users\Model\Orm\User;

/**
 * Регистрация пользователя (клиентом самостоятельно)
 */
class Registration extends AbstractMethod
{
    /** Поля, которые следует ожидать из POST */
    public $use_post_keys = ['is_company', 'company', 'company_inn', 'fio', 'phone', 'e_mail', 'changepass', 'openpass', 'openpass_confirm', 'captcha', 'data'];
    public $user;

    function __construct()
    {
        parent::__construct();
        $this->user     = Auth::getCurrentUser();
    }

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
     * Форматирует комментарий, полученный из PHPDoc
     *
     * @param string $text - комментарий
     * @return string
     */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);

        //Валидатор для пользователя
        $validator = \Users\Model\ApiUtils::getUserRegistrationValidator();
        $text = preg_replace_callback('/\#data-user/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);


        return $text;
    }

    /**
     *  Регистрирует нового пользователя в системе (когда клиент регистрируется самостоятельно)
     *  ---
     *  Метод поддерживает прием параметров только методом POST.
     *  Может потребоваться ввод капчи или подтверждение номера телефона.
     *
     * @param string $client_id id клиентского приложения
     * @param string $client_secret пароль клиентского приложения
     * @param array $user поля пользователя для сохранения #data-user
     *
     * @return array Возвращает информацию об авторизованном пользователе или ошибку
     * @throws \RS\Exception
     * @example POST /api/methods/user.registration?client_id=myapp&client_secret=myappsecret&user[fio]=Иванов Иван Иванович&user[phone]=880008008030&user[e_mail]=admin@admin.ru&user[openpass]=pass123&user[openpass_confirm]=pass123...
     *
     * В случае успешной регистрации:
     *
     * <pre>
     *   {
     *       "response": {
     *           "success": true,
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
     *          "errors": [
     *              "Такой e-mail уже занят",
     *              "Такой телефон уже занят"
     *          ]
     *      }
     *  }</pre>
     *
     */
    protected function process($client_id, $client_secret, $user)
    {
        $app = $this->checkApp($client_id, $client_secret);
        $current_user = $this->getUserForRegistration($user);
        $current_user['creator_app_id'] = $app->getId();

        if (!array_intersect($app->getAllowUserGroup(), $current_user->getUserGroups())) {
            throw new ApiException(t('Пользователь не имеет права доступа к приложению'), ApiException::ERROR_APP_ACCESS_DENIED);
        }

        $current_user->validate();

        if (!$current_user->hasError()) {
            if ($current_user->insert()) {
                $response['response']['success'] = true;

                AppAuth::setCurrentUser($current_user);
                if (AppAuth::onSuccessLogin($current_user, true)) {
                    $token = TokenApi::createToken($current_user['id'], $client_id);
                    $token_data = Login::makeResponseAuthTokenData($token);
                    $auth_user = Login::makeResponseUserData($current_user);

                    //Не передаем пароль в ответе
                    unset($auth_user['openpass'], $auth_user['openpass_confirm']);

                    $response['response'] += [
                        'auth' => $token_data,
                        'user' => $auth_user
                    ];
                } else {
                    $response['response']['success'] = false;
                    $response['response']['errors'] = [AppAuth::getError()];
                }
            } else {
                $response['response']['errors'] = [t('Не удалось создать пользователя.').$current_user->getErrorsStr()];
            }
        }else{
            $response['response']['success'] = false;
            $response['response']['errors'] = $current_user->getErrors();;
        }

        return $response;
    }

    /**
     * Возвращает объект пользователя с включенными необходимыми чекерами для валидации при регистрации
     * и заполненными данными для регистрации
     *
     * @param $user_post_data
     * @return User
     */
    private function getUserForRegistration($user_post_data)
    {
        $current_user = new User();

        $current_user['__fio']->setChecker([User::class, 'checkFioField']);
        $current_user['__name']->removeAllCheckers();
        $current_user['__surname']->removeAllCheckers();
        $current_user['__midname']->removeAllCheckers();

        $system_config = Loader::getSystemConfig();
        $mobilesiteapp_config = Loader::byModule('mobilesiteapp');
        if (!$this->user['__phone']->isEnabledVerification() && $mobilesiteapp_config['captcha'] != 'none') {
            if (
                ($mobilesiteapp_config['captcha'] == 'system' && $system_config['captcha_class'] != 'stub')
                || $mobilesiteapp_config['captcha'] == 'RS-default'
            ) {
                $current_user['__captcha']->setEnable(true);
                $system_config['captcha_class'] = 'RS-default';
            }
        }

        $current_user['changepass'] = 1;
        $current_user->enableOpenPassConfirm();

        return $current_user->getFromArray($user_post_data);
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