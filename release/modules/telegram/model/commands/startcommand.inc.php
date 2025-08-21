<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Commands;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Telegram\Model\Mode\DefaultMode;
use Telegram\Model\Mode\LoginMode;
use Telegram\Model\Mode\LogoutMode;
use Telegram\Model\Orm\TelegramChat;
use Telegram\Model\Orm\TelegramUser;
use Telegram\Model\RsTelegram\TgBot;

/**
 * Обработчик команды /start, создает пользователя Телеграм в базе данных
 * Включает пользователю режим (mode) - default
 */
class StartCommand extends AbstractSystemCommand
{
    /**
     * Telegram object
     *
     * @var TgBot
     */
    protected $telegram;

    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Обработчик исполнения команды
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $profile = $this->telegram->profile;

        if ($this->tg_user['status'] != TelegramUser::STATUS_MEMBER) {
            $this->tg_user['status'] = TelegramUser::STATUS_MEMBER;
            $this->tg_user->update();
        }
        $tg_chat = $this->resetChatState($message, $this->tg_user, $profile);

        if ($profile['welcome_message']) {
            $text = TgBot::htmlToMarkdown($profile['welcome_message']);
        } else {
            $text = t('Добро пожаловать в чат!');
        }

        $reply_markup = [];
        if ($profile['show_reply_markup']) {
            $reply_markup['reply_markup'] = static::getDefaultKeyboard();
        } else {
            $reply_markup['reply_markup'] = Keyboard::remove(['selective' => true]);
        }

        Request::sendMessage([
            'chat_id' => $message->getChat()->getId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
        ] + $reply_markup);

        $tg_chat->switchMode($this, new DefaultMode());

        return Request::emptyResponse();
    }

    /**
     * Возвращает стандартный набор кнопок дополнительной клавиатуры
     *
     * @return Keyboard
     */
    public static function getDefaultKeyboard()
    {
        $keyboard = new Keyboard([
            new KeyboardButton(['text' => LoginMode::getFirstTextCommand()]),
            new KeyboardButton(['text' => LogoutMode::getFirstTextCommand()]),
        ], [
            new KeyboardButton(['text' => DefaultMode::getFirstTextCommand()]),
        ]);
        $keyboard->setResizeKeyboard(true);

        return $keyboard;
    }

    /**
     * Сбрасывает на дефолтный режим
     *
     * @param Message $message
     * @param $telegram_user
     * @param $profile
     * @return TelegramChat
     */
    protected function resetChatState(Message $message, $telegram_user, $profile)
    {
        return TelegramChat::reset(
            $telegram_user['external_id'],
            $profile['id'],
            $message->getChat()->getId()
        );
    }
}