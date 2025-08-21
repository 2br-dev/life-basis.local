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
use RS\Http\Request;

class Sms extends AbstractProvider
{
    protected $template = '%users%/verification/verification_notice_sms.tpl';

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
        if ($session['phone']) {
            $domain = Request::commonInstance()->getDomainStr();

            SmsManager::send(
                $session['phone'],
                $this->template,
                [
                    'operation' => $session->getAction()->getRpTitle(),
                    'domain' => $domain,
                    'code' => $code
                ],
                false
            );

            return true;
        }

        throw new Exception(t('Не задан телефон'));
    }

    /**
     * Возвращает название
     *
     * @return mixed
     */
    public static function getTitle()
    {
        return t('СМС');
    }

    /**
     * Возвращает строковый идентификатор провайдера
     * @return mixed
     */
    public static function getId()
    {
        return 'sms';
    }

    /**
     * Возвращает true, если данный провайдер подходит для верификации через номер телефона
     *
     * @return bool
     */
    public function canSelectForPhoneVerification()
    {
        return true;
    }

    /**
     * Возвращает текст с информацией о том, куда отправлен код
     *
     * @return string
     */
    public function getRecipientText()
    {
        $session = $this->getVerificationSession();
        return t('Код отправлен на номер %number', [
            'number' => $this->getRecipientMask($session['phone'])
        ]);
    }

    /**
     * Возвращает номер телефона с пропусками в виде звездочек
     *
     * @return string
     */
    protected function getRecipientMask($phone)
    {
        if ($phone) {
            return substr($phone, 0, 6) . '****' . substr($phone, 10);
        }
        return '';
    }
}