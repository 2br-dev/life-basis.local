<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Verification\Action;

use RS\Application\Auth as AppAuth;
use Users\Model\Orm\User;
use Users\Model\Verification\Action\AbstractVerifyTypeCode;
use Users\Model\Verification\VerificationException;

class SignIn extends AbstractVerifyTypeCode
{

    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     * @return string
     */
    public function getRpTitle()
    {
        return t('авторизации в Telegram');
    }

    /**
     * Метод вызывается при успешном прохождении верификации
     *
     * @return bool
     * @throws VerificationException
     */
    public function resolve()
    {
        $session = $this->getVerificationSession();

        $user = new User($session['creator_user_id']);
        $success = AppAuth::onSuccessLogin($user, false);

        if ($success) {
            return true;
        } else {
            throw new VerificationException(AppAuth::getError());
        }
    }
}