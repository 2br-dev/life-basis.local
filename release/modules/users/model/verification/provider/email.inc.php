<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Provider;

use Alerts\Model\SMS\Manager as SmsManager;
use RS\Exception;
use RS\Helper\Mailer;
use RS\Http\Request;
use RS\View\Engine;
use Users\Model\Orm\VerificationSession;

class Email extends AbstractProvider
{
    protected $template = '%users%/verification/verification_notice_email.tpl';

    /**
     * Доставляет код к пользователю
     *
     * @param string $code Код верификации
     * @return bool
     *
     * @throws Exception
     * @throws \Users\Model\Verification\VerificationException
     */
    function send(&$code)
    {
        $session = $this->getVerificationSession();
        $email = $this->getRecipientEmail($session);

        if ($email) {
            $domain = Request::commonInstance()->getDomainStr();

            $view = new Engine();
            $view->assign([
                'data' => [
                    'domain' => $domain,
                    'session' => $session,
                    'code' => $code,
                    'operation' => $session->getAction()->getRpTitle()
                ]
            ]);

            $mailer = new Mailer();
            $mailer->Subject = t('Код для %operation на сайте %domain', [
                'operation' => $session->getAction()->getRpTitle(),
                'domain' => $domain
            ]);
            $mailer->addAddress($email);
            $mailer->Body = $view->fetch($this->template);
            if ($mailer->send()) {
                return true;
            } else {
                throw new Exception(t('Не удалось отправить письмо'));
            }
        }

        throw new Exception(t('У пользователя не задан Email'));
    }

    /**
     * Возвращает название
     *
     * @return mixed
     */
    public static function getTitle()
    {
        return t('Email');
    }

    /**
     * Возвращает строковый идентификатор провайдера
     * @return mixed
     */
    public static function getId()
    {
        return 'email';
    }

    /**
     * Возвращает true, если данный провайдер подходит для верификации через номер телефона
     *
     * @return bool
     */
    public function canSelectForPhoneVerification()
    {
        return false;
    }

    /**
     * Возвращает текст с информацией о том, куда отправлен код
     *
     * @return string
     */
    public function getRecipientText()
    {
        $session = $this->getVerificationSession();
        return t('Код отправлен на почту %email', [
            'email' => $this->getRecipientMask($this->getRecipientEmail($session))
        ]);
    }

    /**
     * Возвращает email получателя верификационного кода
     *
     * @param VerificationSession $session
     * @return string
     */
    protected function getRecipientEmail(VerificationSession $session)
    {
        $probably_user = $session->getCreatorUser();
        return $session['email'] ?: $probably_user['e_mail'];
    }

    /**
     * Возвращает email с пропусками в виде звездочек
     *
     * @return string
     */
    protected function getRecipientMask($email)
    {
        if ($email && strpos($email, '@') !== false) {
            list($user, $host) = explode('@', $email);

            if (mb_strlen($user) > 4) {
                $user = mb_substr($user, 0, 1).'****'.mb_substr($user, -1);
            } else {
                $user = mb_substr($user, 0, 1).'****';
            }

            return $user.'@'.$host;
        }

        return '';
    }
}