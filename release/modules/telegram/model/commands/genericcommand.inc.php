<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Telegram\Model\Mode\AbstractMode;
use Telegram\Model\RsTelegram\TgBot;

/**
 * Обрабатывает команды от Telegram (/команда),
 * Обеспечивает переключение между режимами
 */
class GenericCommand extends AbstractSystemCommand
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
    protected $name = 'generic';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $update = $this->getUpdate();
        if ($update
                && ($chat_member = $update->getMyChatMember())
                && ($new_chat_member = $chat_member->getNewChatMember()))
        {
            //Если пользователь был удален, обновим статус
            $this->tg_user['status'] = $new_chat_member->getStatus();
            $this->tg_user->update();

            return Request::emptyResponse();
        }

        $edit_message = $this->getEditedMessage();
        if ($edit_message) {
            $chat = $this->tg_user->getTelegramChat($this->telegram->profile['id'], $edit_message->getChat()->getId());
            $mode = $chat->getModeObject();
            $mode->onMessage($this);
            return Request::emptyResponse();
        }

        $message = $this->getMessage();
        if ($message)
        {
            $chat = $this->tg_user->getTelegramChat($this->telegram->profile['id'], $message->getChat()->getId());

            $text = ltrim($message->getFullCommand(), '/');
            $mode = AbstractMode::getById($text, $chat, false);

            if ($mode) {
                $chat->switchMode($this, $mode);
            } else {
                return Request::sendMessage([
                    'chat_id' => $chat->chat_id,
                    'text' => TgBot::htmlToMarkdown(t('Неизвестный режим работы %0, попробуйте указать другой', [$text])),
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true
                ]);
            }
        }

        return Request::emptyResponse();
    }
}