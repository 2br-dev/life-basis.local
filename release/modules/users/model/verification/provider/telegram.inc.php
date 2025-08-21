<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Provider;

use Main\Model\Requester\ExternalRequest;
use RS\Exception;
use Users\Config\File;
use Users\Model\Verification\VerificationProviderManager;

/**
 * Провайдер подтверждения через Telegram Gateway
 */
class Telegram extends AbstractProvider
{
    const BASE_URL = 'https://gatewayapi.telegram.org/';
    protected $used_provider;

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
    public function send(&$code)
    {
        $this->used_provider = $this;
        $session = $this->getVerificationSession();
        if ($session['phone']) {
            $config = File::config();
            $send_data = $this->request('sendVerificationMessage', [
                'phone_number' => $session['phone'],
                'sender_username' => $config['telegram_gw_sender_username'],
                'code' => $code,
                'ttl' => $config['lifetime_code_minutes'] * 60
            ]);

            if ($send_data['ok'] || $send_data['error'] == 'MESSAGE_ALREADY_SENT') {
                return true;
            } else {
                if ($config['telegram_gw_alternate_provider']) {

                    $this->used_provider = VerificationProviderManager::getProviderByName($config['telegram_gw_alternate_provider']);
                    $this->used_provider->setVerificationSession($session);
                    return $this->used_provider->send($code);

                } else {
                    $errors = [
                        'SENDER_USERNAME_INVALID' => t('Недопустимое имя канала-отправителя'),
                        'SENDER_NOT_VERIFIED' => t('Канал-отправитель не верифицирован'),
                        'SENDER_NOT_OWNED' => t('Канал-отправитель не принадлежит пользователю'),
                        'PHONE_NUMBER_INVALID' => t('Недопустимый номер телефона'),
                        'PHONE_NUMBER_NOT_FOUND' => t('Не найден пользователь по такому номеру'),
                        'BALANCE_NOT_ENOUGH' => t('Недостаточно средств на балансе Telegram Gateway'),
                        'TTL_INVALID' => t('Некорректный срок действия кода'),
                        'ACCESS_TOKEN_INVALID' => t('Недопустимый токен доступа'),
                        'ACCESS_TOKEN_REQUIRED' => t('Токен доступа к Telegram Gateway не указан'),
                        'CODE_MAX_ATTEMPTS_EXCEEDED' => t('Превышено количество попыток отправки кода')
                    ];
                    $error = $errors[$send_data['error']] ?? t('Ошибка: %0', [$send_data['error']]);
                    throw new Exception($error);
                }
            }
        }

        throw new Exception(t('Не задан телефон'));
    }

    /**
     * Выполняет запрос к telegram
     *
     * @return array|false
     */
    protected function request($endpoint, $parameters = [])
    {
        $config = File::config();
        $url = self::BASE_URL.'/'.$endpoint.'?'.http_build_query($parameters);
        $requester = new ExternalRequest('telegram-gateway', $url);
        $requester->setAuthorization('Bearer '.$config['telegram_gw_api']);
        $requester->setContentType(ExternalRequest::CONTENT_TYPE_JSON_WITHOUT_CHARSET);
        $requester->setMethod(ExternalRequest::METHOD_POST);
        $requester->setParams($parameters);
        $response = $requester->executeRequest();
        return $response->getResponseJson();
    }

    /**
     * Возвращает название провайдера доставки кодов
     *
     * @return mixed
     */
    public static function getTitle()
    {
        return t('Telegram Gateway');
    }

    /**
     * Возвращает строковый идентификатор провайдера
     * @return mixed
     */
    public static function getId()
    {
        return 'telegram-gateway';
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
     * Предварительно должен быть установлен объект верификационной сессии
     *
     * @return string
     */
    public function getRecipientText()
    {
        if (!$this->used_provider || $this->used_provider === $this) {
            $session = $this->getVerificationSession();
            return t('Код отправлен в Telegram на номер %number', [
                'number' => $this->getRecipientMask($session['phone'])
            ]);
        } else {
            return $this->used_provider->getRecipientText();
        }
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