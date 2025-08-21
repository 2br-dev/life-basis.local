<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Provider;

use RS\Exception;
use Users\Model\Orm\VerificationSession;

/**
 * Абстрактный класс провайдера доставки проверочного кода для верификации.
 * Например, SMS или Email
 */
abstract class AbstractProvider
{
    private $verification_session;

    /**
     * Устанавливает объект верификационной сессии, с которой будут работать остальные методы
     *
     * @param VerificationSession $session
     */
    function setVerificationSession(VerificationSession $session)
    {
        $this->verification_session = $session;
    }

    /**
     * Возвращает объект верификационной сессии
     *
     * @param bool $throw_exception_if_not_set Если true, то будет брошено исключение, если верификационная сессия не установлена
     * @return VerificationSession
     * @throws Exception
     */
    function getVerificationSession($throw_exception_if_not_set = true)
    {
        if (!$this->verification_session && $throw_exception_if_not_set) {
            throw new Exception(t('Не установлена верификационная сессия'));
        }
        return $this->verification_session;
    }

    /**
     * Доставляет код к пользователю.
     * Предварительно должен быть установлен объект верификационной сессии.
     *
     * Внутри метода, $code может быть изменен, что открывает возможность для подключения сервисов вроде CallPassword,
     * которые генерируют код, только после запроса к этим сервисам.
     *
     * @param string $code Код верификации
     * @return bool
     * @throws Exception Бросает исключение в случае ошибки
     */
    abstract public function send(&$code);

    /**
     * Возвращает название
     *
     * @return mixed
     */
    abstract public static function getTitle();

    /**
     * Возвращает строковый идентификатор провайдера
     * @return mixed
     */
    abstract public static function getId();

    /**
     * Возвращает true, если данный провайдер подходит для верификации через номер телефона
     *
     * @return bool
     */
    abstract public function canSelectForPhoneVerification();

    /**
     * Возвращает текст с информацией о том, куда отправлен код
     * Предварительно должен быть установлен объект верификационной сессии
     *
     * @return string
     */
    abstract public function getRecipientText();


    /**
     * Возвращает текст о том, через сколько можно будет повторить попытку получение кода.
     * По умолчанию это: "Отправить новый код можно через" далее будет всегда добавлено XX сек.
     *
     * @return string
     */
    public function getReSendCodeText()
    {
        return t('Отправить новый код можно через');
    }
}