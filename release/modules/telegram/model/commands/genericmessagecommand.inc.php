<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Telegram\Model\Mode\AbstractMode;
use Telegram\Model\RsTelegram\TgBot;

/**
 * Обрабатывает сообщения от Telegram
 */
class GenericMessageCommand extends AbstractSystemCommand
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
    protected $name = Telegram::GENERIC_MESSAGE_COMMAND;

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
        $message = $this->getMessage();
        $result = Request::emptyResponse();

        if ($message) {
            if ($command = $this->findCommandByText($message->getText())) {
                //Если это текстовая команда, то запускаем обработчик комманд
                $update = json_decode($this->update->toJson(), true);
                $update['message']['text'] = $command;
                $result = (new GenericCommand($this->telegram, new Update($update)))->preExecute();
            } else {
                $chat = $this->tg_user->getTelegramChat($this->telegram->profile['id'], $message->getChat()->getId());
                $mode = $chat->getModeObject();
                $mode->onMessage($this);
            }
        }

        return $result;
    }

    /**
     * Возвращает true, если это текстовая команда, а не обычное сообщение
     *
     * @param string $text
     * @return bool
     */
    public function findCommandByText($text)
    {
        $modes = AbstractMode::getModes();
        foreach($modes as $mode) {
            foreach($mode->getTextCommands() as $command) {
                if (mb_strtolower($command) === mb_strtolower($text)) {
                    return '/'.$mode->getId();
                }
            }
        }

        return false;
    }
}