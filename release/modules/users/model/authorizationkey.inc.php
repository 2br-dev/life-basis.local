<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;

use RS\Config\Loader;
use RS\Exception;
use RS\Helper\QrCode\QrCodeGenerator;
use RS\Helper\Tools;
use RS\Router\Manager;
use Users\Config\File as UsersConfig;
use Users\Model\Orm\User;

/**
 * Класс описывает объект "Ключ авторизации", используемый для авторизации через QR-код.
 * Ключ работает по принципу JWT-токена, содержит полезную нагрузку и подпись.
 */
class AuthorizationKey
{
    const USER_SEARCH_KEY_LENGTH = 15;
    const RANDOM_STRING_LENGTH = 10;

    private User $user;
    private array $payload = [];

    /**
     * Используйте для создания объекта методы makeByUser, makeByKey
     *
     * @param User $user Пользователь, для которого создается авторизационный ключ
     * @param array $payload Полезная нагрузка
     */
    private function __construct($user, $payload)
    {
        $this->user = $user;
        $this->payload = $payload;
    }

    /**
     * Создает новый ключ авторизации для пользователя
     *
     * @param User $user
     * @return self
     */
    public static function makeByUser(User $user)
    {
        $config = UsersConfig::config();

        $payload = [
            'user' => Tools::generatePassword(self::USER_SEARCH_KEY_LENGTH, 'abcdefghijklmnopqrstuvwxyz0123456789'),
            'rand' => Tools::generatePassword(self::RANDOM_STRING_LENGTH),
            'expire' => time() + $config['auth_key_lifetime'],
        ];

        if ($user->isAdmin()
            && ($external_api_config = Loader::byModule('externalapi'))) {
            $payload['api_key'] = $external_api_config['api_key'];
        }

        $user['public_id'] = $payload['user'];
        $user->update();

        return new self($user, $payload);
    }

    /**
     * Создает объект текущего класса по строковому авторизационному ключу
     *
     * @param string $key
     * @return self
     * @throws Exception
     */
    public static function makeByKey($key)
    {
        $config = UsersConfig::config();
        if (!$config['auth_by_key_enable']) {
            throw new Exception(t('Авторизация по QR-коду отключена'));
        }

        if ($key == '') {
            throw new Exception(t('Не передан авторизационный ключ'));
        }

        $parts = explode('.', $key);
        if (count($parts) != 2) {
            throw new Exception(t('Неверный формат ключа'));
        }

        [$payload_in_base64, $sign] = $parts;
        $payload = json_decode(base64_decode($payload_in_base64), true);
        if (!$payload || !is_array($payload)) {
            throw new Exception(t('Ошибка декодирования ключа. Неверный формат'));
        }

        if ($sign !== self::getSign($payload_in_base64)) {
            throw new Exception(t('Неверная подпись ключа'));
        }

        if (strlen((string)$payload['user']) < self::USER_SEARCH_KEY_LENGTH) {
            throw new Exception(t('Неверная длина идентификатора пользователя'));
        }

        if (preg_match('/[^a-z0-9]/', (string)$payload['user'])) {
            throw new Exception(t('Некорректный идентификатора пользователя'));
        }

        $expire = (int)($payload['expire'] ?? 0);
        if ($expire < time()) {
            throw new Exception(t('Срок действия ключа истек'));
        }

        $user = User::loadByWhere([
            'public_id' => $payload['user']
        ]);
        if (!$user['id']) {
            throw new Exception(t('Пользователь не найден'));
        }

        $user['public_id'] = null;
        $user->update();

        return new self($user, $payload);
    }

    /**
     * Возвращает полезную нагрузку
     *
     * @param bool $in_base64 Если true, то результат будет строка base64
     *
     * @return array|string
     */
    public function getPayload($in_base64 = false)
    {
        return $in_base64 ? base64_encode(json_encode($this->payload)) : $this->payload;
    }

    /**
     * Возвращает ключ авторизации
     *
     * @return string
     */
    public function getAuthKey()
    {
        $payload_in_base64 = $this->getPayload(true);
        $sign = self::getSign($payload_in_base64);

        return $payload_in_base64.'.'.$sign;
    }

    /**
     * Возвращает ссылку на авторизацию на сайте
     *
     * @return string
     */
    public function getAuthLink()
    {
        return Manager::obj()->getUrl('users-front-auth', ['Act' => 'byQRCode',
            'key' => $this->getAuthKey()], true);
    }

    /**
     * Возвращает ссылку на картинку с QR-кодом для авторизации под текущим пользователем.
     * Зашитая в QR-код ссылка будет действовать ограниченное время.
     *
     * @param int $width Ширина картинки
     * @param int $height Высота картинки
     *
     * @return string
     */
    public function getQrCodeImageLink($width = 300, $height = 300, $auth_link = null)
    {
        return QrCodeGenerator::buildUrl($auth_link ?? $this->getAuthLink(), [
            'w' => $width,
            'h' => $height
        ]);
    }

    /**
     * Возвращает количество секунд, оставшееся до истечения срока действия ключа
     *
     * @return integer
     */
    public function getExpireLeftSeconds()
    {
        $left_seconds = ($this->getPayload()['expire'] ?? 0) - time();
        return max($left_seconds, 0);
    }

    /**
     * Возвращает объект пользователя, связанного с авторизационным ключем
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Возвращает публичную подпись полезной нагрузки
     *
     * @param string $payload_in_base64 Полезная нагрузка
     * @return string
     */
    private static function getSign($payload_in_base64)
    {
        return hash_hmac('sha384', $payload_in_base64, \Setup::$SECRET_KEY. sha1(\Setup::$SECRET_SALT));
    }
}