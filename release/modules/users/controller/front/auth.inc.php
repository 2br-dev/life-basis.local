<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Front;

use RS\Application\Auth as AppAuth;
use RS\Controller\Front;
use RS\Controller\Result\Standard;
use RS\Exception;
use RS\Orm\Type\Checker;
use Users\Config\File;
use Users\Model\Api as UserApi;
use Users\Model\AuthorizationKey;
use Users\Model\Orm\User;
use Users\Model\Verification\Action\TwoStepAuthorize;
use Users\Model\Verification\Action\TwoStepRecoverPassword;
use Users\Model\Verification\Action\TwoStepRegisterByPhone;
use Users\Model\Verification\VerificationEngine;
use RS\Helper\Tools as HelperTools;

/**
* Контроллер авторизации клиентской части.
* Поддерживает 3 сценария авторизации: стандартный, двухфакторный по логину и паролю, двухфакторный только по телефону
* Поддерживает восстановление пароля по Email'у и телефону с кодом подтверждения
*/
class Auth extends Front
{
    /**
     * @var UserApi
     */
    protected $user_api;

    /**
     * Запускается перед любым action
     */
    function init()
    {
        $this->user_api = new UserApi();
    }

    /**
     * Авторизация
     *
     * @param bool $force_standard
     * @return Standard
     */
    public function actionIndex()
    {
        $this->app->breadcrumbs->addBreadCrumb(t('Авторизация'));

        $this->app->title->addSection(t('Авторизация'));
        $this->app->meta->addDescriptions(t('На этой странице можно авторизоваться'));

        $referer = $this->url->request('referer', TYPE_STRING, \RS\Site\Manager::getSite()->getRootUrl());
        $referer = \RS\Helper\Tools::cleanOpenRedirect( urldecode($referer) );
        $error = '';

        $data = [
            'login' => $this->url->request('login', TYPE_STRING),
            'pass' => $this->url->request('pass', TYPE_STRING, '', false),
            'remember' => $this->url->request('remember', TYPE_BOOLEAN),
            'referer' => urlencode($referer)
        ];

        if ($this->isMyPost()) {
            $user = AppAuth::login($data['login'], $data['pass'], $data['remember'], false, true);

            if ($user) {
                if ($this->getModuleConfig()->isEnabledTwoFactorAuthorization($user)) {
                    // Если включена двухэтапная авторизация, то инициализируем
                    // второй фактор и редиректим на страницу верификации

                    $verification_action = new TwoStepAuthorize();
                    $verification_action->setRememberUser($data['remember']);
                    $verification_action->setReferer(urldecode($data['referer']));

                    $verification_engine = (new VerificationEngine())
                        ->setCreatorUserId($user['id'])
                        ->setAction($verification_action)
                        ->setPhone($user['phone']);

                    $verification_engine->initializeSession();

                    if (!$verification_engine->getSession()->isResolved()) {
                        $token = $verification_engine->getSession()->getToken();
                        return $this->result->setRedirect($this->router->getUrl('users-front-auth', [
                            'Act' => 'verify',
                            'token' => $token
                        ]));
                    }
                }

                //Завершаем авторизацию
                AppAuth::setCurrentUser($user);
                $success = AppAuth::onSuccessLogin($user, $data['remember']);
                $this->result->setSuccess($success);

                if ($success) {
                    return $this->result
                        ->setNoAjaxRedirect($referer)
                        ->addSection('reloadPage', true);
                }
            }

            $error = AppAuth::getError();
        }

        $this->view->assign([
            'status_message' => $_SESSION['auth_access_error'] ?? '',
            'error' => $error,
            'referrer' => $referer,
            'login_placeholder' => $this->user_api->getAuthLoginPlaceholder(),
            'data' => $data
        ]);
        unset($_SESSION['auth_access_error']);

        return $this->result->setTemplate('authorization.tpl');
    }

    /**
     * Второй шаг авторизации
     *
     * @return Standard
     */
    public function actionVerify()
    {
        $this->app->breadcrumbs->addBreadCrumb(t('Авторизация'));

        $this->app->title->addSection(t('Авторизация'));
        $this->app->meta->addDescriptions(t('Верификация авторизации'));

        $token = $this->url->get('token', TYPE_STRING);

        if ($this->getModuleConfig()->type_auth == File::TYPE_AUTH_STANDARD
            && !$this->getModuleConfig()->two_factor_recover) {
            $this->e404(t('Верификация при текущих настройках невозможна'));
        }

        //Инициализируем движок верификации
        $verification_engine = new VerificationEngine();
        if (!$verification_engine->initializeByToken($token)) {
            $this->e404($verification_engine->getErrorsStr());
        }

        $session = $verification_engine->getSession();

        if ($this->isMyPost()) {
            $code = $this->url->post('code', TYPE_STRING);
            $result = $verification_engine->checkCode($code);
            if ($result !== false) {
                //Все успешно
                if ($result instanceof Standard) {
                    $result->setController($this);
                }

                return $result;
            }
        } else {
            //Автоматически отправляем код при входе на страницу
            if ($session->getRefreshCodeDelay() == 0) {
                $verification_engine->sendCode();
            }
        }

        $this->view->assign([
            'verification_engine' => $verification_engine,
            'token' => $token,
        ]);

        return $this->result
                        ->setSuccess(true)
                        ->setTemplate('authorization_two_step.tpl');
    }

    /**
     * Авторизация по телефону
     *
     * @return Standard
     */
    protected function actionByPhone()
    {
        $this->app->breadcrumbs->addBreadCrumb(t('Авторизация'));

        $this->app->title->addSection(t('Авторизация'));
        $this->app->meta->addDescriptions(t('Авторизация по номеру телефона'));

        if ($this->getModuleConfig()->type_auth != File::TYPE_AUTH_PHONE) {
            $this->e404(t('Данный тип авторизации отключен в настройках'));
        }

        $referer = $this->url->request('referer', TYPE_STRING, \RS\Site\Manager::getSite()->getRootUrl());
        $referer = \RS\Helper\Tools::cleanOpenRedirect( urldecode($referer) );

        $data = [
            'phone' => $this->url->request('phone', TYPE_STRING),
            'remember' => $this->url->request('remember', TYPE_BOOLEAN),
            'referer' => urlencode($referer)
        ];

        if ($this->isMyPost() && $data['phone'] != '') {

            $check_phone_result = Checker::chkPhone(null, $data['phone']);
            if ($check_phone_result === true) {

                $phone = UserApi::normalizePhoneNumber($data['phone']);
                $tmp_user = User::loadByWhere([
                    'phone' => $phone
                ]);

                if ($tmp_user['id']) {
                    //Пробуем авторизовать пользователя
                    if (AppAuth::login($tmp_user['phone'], $tmp_user['pass'], false, true, true)) {
                        $verification_action = new TwoStepAuthorize();
                    } else {
                        $error = AppAuth::getError();
                    }
                } elseif ($this->getModuleConfig()->register_by_phone) {
                    //Регистрация и авторизация пользвателя
                    $tmp_user['phone'] = $phone;
                    $verification_action = new TwoStepRegisterByPhone();
                } else {
                    //Ошибка
                    $error = t('Пользователь с таким номером телефона не найден');
                }

                if (isset($verification_action)) {
                    $verification_action->setRememberUser($data['remember']);
                    $verification_action->setReferer(urldecode($data['referer']));

                    $verification_engine = (new VerificationEngine())
                        ->setCreatorUserId($tmp_user['id'])
                        ->setAction($verification_action)
                        ->setPhone($tmp_user['phone']);

                    $verification_engine->initializeSession();

                    $token = $verification_engine->getSession()->getToken();

                    return $this->result->setRedirect($this->router->getUrl('users-front-auth', [
                        'Act' => 'verify',
                        'token' => $token
                    ]));
                }

            } else {
                $error = $check_phone_result;
            }

        }

        $this->view->assign([
            'status_message' => $_SESSION['auth_access_error'] ?? '',
            'data' => $data,
            'error' => $error ?? ''
        ]);
        unset($_SESSION['auth_access_error']);

        return $this->result->setTemplate('authorization_by_phone.tpl');
    }

    /**
     * Авторизация по QR-коду
     *
     * @return Standard
     */
    function actionByQRCode()
    {
        $key = $this->url->get('key', TYPE_STRING);
        try {
            $authorizationKey = AuthorizationKey::makeByKey($key);
            $user = $authorizationKey->getUser();

            AppAuth::setCurrentUser($user);
            if (!AppAuth::onSuccessLogin($user, true)) {
                throw new Exception(AppAuth::getError());
            }

        } catch(Exception $e) {
            $this->e404($e->getMessage());
        }

        $referer = HelperTools::cleanOpenRedirect( $this->url->request('referer', TYPE_STRING) );
        $this->app->redirect($referer);
    }

    /**
     * Восстановление пароля
     *
     * @return Standard
     */
    function actionRecover()
    {
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Авторизация'), $this->router->getUrl('users-front-auth'))
            ->addBreadCrumb(t('Восстановление пароля'));

        $this->app->title->addSection(t('Восстановление пароля'));
        $this->app->meta->addDescriptions(t('На этой странице вы можете отправить заявку на восстановление пароля'));
        
        $data = [
            'login' => $this->url->request('login', TYPE_STRING)
        ];
        $error = false;

        if ($this->isMyPost()) {
            $user = AppAuth::getUserByLogin($data['login']);

            if ($user) {
                //Определяем как будем восстанавливать пароль: по email'у или телефону
                $recover_type = $this->user_api->getRecoverTypeByLogin($data['login'], $user);

                if ($recover_type == UserApi::RECOVER_TYPE_PHONE) {

                    $verification_engine = (new VerificationEngine())
                        ->setCreatorUserId($user['id'])
                        ->setAction(new TwoStepRecoverPassword())
                        ->setPhone($user['phone']);

                    $verification_engine->initializeSession();

                    $token = $verification_engine->getSession()->getToken();
                    return $this->result->setRedirect($this->router->getUrl('users-front-auth', [
                        'Act' => 'verify',
                        'token' => $token
                    ]));
                }

                elseif ($recover_type == UserApi::RECOVER_TYPE_EMAIL) {
                    $success = $this->user_api->sendRecoverEmailByUser($user);
                    if (!$success) {
                        $error = $this->user_api->getErrorsStr();
                    }
                }

                else { // none
                    $empty_field = $recover_type == UserApi::RECOVER_TYPE_NONE_EMAIL ? 'e-mail' : t('телефон');
                    $error = t('У пользователя не заполнен %0', [$empty_field]);
                }
            } else {
                $error = t('Пользователь не найден');
            }
        }

        $this->view->assign([
            'login_placeholder' => $this->user_api->getRecoverLoginPlaceholder(),
            'send_success' => $success ?? false,
            'error' => $error,
            'data' => $data
        ]);
        return $this->result->setTemplate( 'recover_pass.tpl' );
    }



    /**
     * Смена пароля
     *
     * @return Standard
     */
    function actionChangePassword()
    {
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Авторизация'), $this->router->getUrl('users-front-auth'))
            ->addBreadCrumb(t('Восстановление пароля'));

        $this->app->title->addSection(t('Восстановление пароля'));
        $this->app->meta->addDescriptions(t('На этой странице вы можете заменить пароль'));
                    
        $hash = $this->url->get('uniq', TYPE_STRING);
        $user = $this->user_api->getByHash($hash);
        if (!$user) {
            return $this->e404();
        }

        $error = '';        
        if ($this->url->isPost()) {
            $new_pass = $this->url->post('new_pass', TYPE_STRING);
            $new_pass_confirm = $this->url->post('new_pass_confirm', TYPE_STRING);
            
            if ($this->user_api->changeUserPassword($user, $new_pass, $new_pass_confirm)) {
                $_SESSION['auth_access_error'] = t('Пароль успешно изменен. Повторите попытку авторизации.');
                $this->app->redirect($this->getModuleConfig()->getAuthorizationUrl());
            } else {
                $error = $this->user_api->getErrorsStr();
            }
        }
        
        $this->view->assign([
            'uniq' => $hash,
            'user' => $user,
            'error' => $error
        ]);
        
        return $this->result->setTemplate( 'change_pass.tpl' );
    }

    /**
     * Подтверждение действующего пароля пользователя
     *
     * @return Standard
     */
    function actionConfirmPassword()
    {
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Авторизация'), $this->router->getUrl('users-front-auth'))
            ->addBreadCrumb(t('Подтверждение пароля'));

        $hash = $this->url->get('uniq', TYPE_STRING);
        $user = $this->user_api->getByHash($hash);
        if (!$user) {
            return $this->e404();
        }

        if ($user->refreshChangeDatePassword()) {
            $_SESSION['auth_access_error'] = t('Пароль успешно подтвержден. Повторите попытку авторизации.');
        } else {
            $_SESSION['auth_access_error'] = $user->getErrorsStr();
        }

        $this->app->redirect($this->getModuleConfig()->getAuthorizationUrl());
    }

    /**
     * Выход
     */
    function actionLogout()
    {
        $referer = HelperTools::cleanOpenRedirect( $this->url->request('referer', TYPE_STRING) );
        AppAuth::logout();
        $this->app->redirect($referer);
    }    
}