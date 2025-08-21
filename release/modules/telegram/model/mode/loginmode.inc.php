<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Mode;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Request;
use RS\Application\Auth;
use RS\Exception;
use Telegram\Config\File;
use Telegram\Model\Commands\AbstractSystemCommand;
use Telegram\Model\Verification\Action\SignIn;
use Users\Model\Api;
use Users\Model\Verification\Provider\Email;
use Users\Model\Verification\Provider\Sms;
use Users\Model\Verification\VerificationEngine;

/**
 * Обеспечивает работу режима авторизации
 */
class LoginMode extends AbstractMode
{
    const SUBMODE_ENTER_LOGIN = 'login';
    const SUBMODE_ENTER_PASSWORD = 'password';
    /**
     * @var Api
     */
    protected $user_api;

    /**
     * @var string
     */
    protected $login_placeholder;

    /**
     * @var File
     */
    protected $config;


    function __construct()
    {
        parent::__construct();
        $this->user_api = new Api();
        $this->login_placeholder = $this->user_api->getAuthLoginPlaceholder();
    }

    /**
     * Обработчик, вызываемый при входе в данный режим
     * @param AbstractSystemCommand $command
     *
     * @return void
     */
    public function onEnterMode($command)
    {
        $chat = $this->getTelegramChat();
        $chat['submode'] = self::SUBMODE_ENTER_LOGIN;
        $chat->addStateData(self::getId(), []);
        $chat->update();

        $profile = $chat->getProfile();
        $config = File::config($profile['site_id']);

        if (!$config['verify_providers']) {
            $chat->switchMode($command, new DefaultMode());
            $command->replyToChat(
                t('Авторизация невозможна. Не настроен ни один канал получения кода верификации. Обратитесь в администрацию сервиса.')
            );
            return;
        }

        $user = $command->tg_user->getRsUser();
        if ($user['id']) {
            $command->replyToChat(
                t('%name, Вы уже авторизованы, напишите другой %fields, если вы желаете сменить пользователя.', [
                    'name' => $user['name'],
                    'fields' => $this->login_placeholder
                ])
            );
        } else {
            $command->replyToChat(t('Введите %fields для авторизации.', [
                'fields' => $this->login_placeholder
            ]));
        }
    }

    /**
     * Обрабатывает команды авторизации
     *
     * @param AbstractSystemCommand $command Объект обработчика команды от Телеграм
     * @return \Longman\TelegramBot\Entities\ServerResponse|void
     * @throws Exception
     */
    public function onMessage($command)
    {
        $chat = $this->getTelegramChat();

        switch($chat['submode']) {
            case self::SUBMODE_ENTER_LOGIN:
                return $this->signIn($command);

            case self::SUBMODE_ENTER_PASSWORD:
                return $this->verify($command);
        }
    }

    /**
     * Возвращает ID сессии для чата с конкретным пользователем
     *
     * @return string
     */
    protected function getChatSessionId()
    {
        $chat = $this->getTelegramChat();
        return 'TELEGRAM-CHAT-'.$chat['telegram_user_id'].'-'.$chat['chat_id'];
    }

    /**
     * Запрашивает Email, Телефон или логин. В случае успеха
     * перебрасывает на следующий шаг - верификацию
     *
     * @param AbstractSystemCommand $command
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    protected function signIn($command)
    {
        $message = $command->getMessage();
        $chat = $this->getTelegramChat();
        $login = $message->getText();

        if (!$login) {
            return $command->replyToChat(t('Введите текстом %placeholder', [
                'placeholder' => $this->login_placeholder
            ]));
        }

        $user = Auth::getUserByLogin($login);
        if (!$user) {
            return $command->replyToChat(t('Пользователь не найден, введите %placeholder еще раз', [
                'placeholder' => $this->login_placeholder
            ]));
        }

        $profile = $chat->getProfile();
        $config = File::config($profile['site_id']);

        //Создаем верификационную сессию для авторизации
        if ($user['e_mail'] != '' && in_array(Email::getId(), (array)$config['verify_providers'])) {
            $verification_provider = new Email();
        } elseif ($user['phone'] != '' && in_array(Sms::getId(), (array)$config['verify_providers'])) {
            $verification_provider = new Sms();
        } else {
            return $command->replyToChat(t('Невозможно отправить код верификации данному пользователю. Укажите другой %placeholder', [
                'placeholder' => $this->login_placeholder
            ]));
        }

        $verify_engine = new VerificationEngine();
        $verify_engine->setAction(new SignIn());
        $verify_engine->setCreatorUserId($user['id']);
        $verify_engine->setVerificationProvider($verification_provider);
        if ($verification_provider instanceof Sms) {
            $verify_engine->setPhone($user['phone']);
        }
        $verify_engine->setSessionId($this->getChatSessionId());
        $verify_engine->initializeSession();
        $session = $verify_engine->getSession();

        if ($verify_engine->sendCode()) {
            if ($session['code_debug']) {
                $command->replyToChat(t('Ваш код(включен DEMO-режим): %code', [
                    'code' => $session['code_debug']
                ]));
            }

            //Переводим на ввод пароля
            $chat['submode'] = self::SUBMODE_ENTER_PASSWORD;
            $chat->addStateData(self::getId(), [
                'verification_session_token' => $session['uniq']
            ]);
            $chat->update();
            return $command->replyToChat(t('Введите код верификации, отправленный через %channel', [
                'channel' => $session->getVerificationProvider()->getTitle()
            ]));
        } else {
            return $command->replyToChat($verify_engine->getErrorsStr()."\n".t('Введите повторно %placeholder для авторизации', [
                    'placeholder' => $this->login_placeholder
            ]));
        }
    }

    /**
     * Проверяет верификационный код. В случае успешного ввода - авторизует пользователя
     *
     * @param AbstractSystemCommand $command
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    protected function verify($command)
    {
        $message = $command->getMessage();
        $chat = $this->getTelegramChat();
        $code = $message->getText();

        $state = $chat->getStateData(self::getId());
        $verification_session_token = $state['verification_session_token'] ?? '';

        $verify_engine = new VerificationEngine();
        $verify_engine->setSessionId($this->getChatSessionId());

        if (!$verification_session_token
            || !$verify_engine->initializeByToken($verification_session_token)) {

            $chat['submode'] = self::SUBMODE_ENTER_LOGIN;
            $chat->addStateData(self::getId(), []);
            $chat->update();

            return $command->replyToChat(
                t('Вероятно верификационная сессия уже истекла. Попробуйте повторно ввести %placeholder.', [
                    'placeholder' => $this->login_placeholder
                ])
            );
        }

        if ($verify_engine->checkCode($code)) {
            $session = $verify_engine->getSession();
            $user = $session->getCreatorUser();

            $command->tg_user['user_id'] = $user['id'];
            $command->tg_user->update();

            $command->replyToChat(
                t('Приветствуем Вас, %name! Вы успешно авторизованы. Возвращаем вас в режим поддержки.', [
                    'name' => ucfirst($user['name'])
                ])
            );

            $chat->switchMode($command, new DefaultMode());
            return Request::emptyResponse();
        } else {
            return
                $command->replyToChat(
                    $verify_engine->getErrorsStr()
                );
        }
    }

    /**
     * Возвращает ID режима работы
     *
     * @return string
     */
    public static function getId()
    {
        return 'login';
    }

    /**
     * Возвращает название режима работы
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('Авторизация');
    }

    /**
     * Возвращает текстовые идентификаторы данной команды.
     * Т.е. если в чат будет отправлено сообщение, совпадающее с элементами данного массива,
     * то будет запущена обработка команды.
     *
     * @return array
     */
    public static function getTextCommands()
    {
        return [t('🔑 Вход')];
    }
}