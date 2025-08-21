<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Commands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use RS\Event\Manager;
use RS\Exception;
use Telegram\Model\Log\TelegramLog;
use Telegram\Model\Orm\TelegramUser;

/**
 * Базовый класс для обработчиков команд ReadyScript от Телеграмма
 */
abstract class AbstractSystemCommand extends SystemCommand
{
    /**
     * Пользователь Telegram
     *
     * @var $tg_user TelegramUser
     */
    public $tg_user;
    private $mode;

    /**
     * Обработчик всех поступающих от Telegram сообщений
     *
     * @return ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function preExecute(): ServerResponse
    {
        $log = TelegramLog::getInstance();

        try {
            $event_result = Manager::fire('telegram.command.beforeExecute', [
                'command' => $this,
            ]);
            if ($event_result->getEvent()->isStopped()) {
                return Request::emptyResponse();
            }

            $message = $this->getMessage();
            if ($message) {
                $from = $message->getFrom();
            } elseif ($this->update->getMyChatMember()) {
                $from = $this->getMyChatMember()->getFrom();
            } elseif ($this->update->getEditedMessage()) {
                $from = $this->getEditedMessage()->getFrom();
            }

            if (isset($from)) {
                $this->tg_user = TelegramUser::getByTgUser($from);
            } else {
                //Обрабатываем только те команды, у которых мы можем определить пользователя
                $log->write(t('Ошибка: Невозможно определить пользователя, пропускаем команду'), TelegramLog::LEVEL_INFO);
                return Request::emptyResponse();
            }

            if ($this->tg_user->isBanned()
                || ($this->tg_user->getRsUser()->ban_expire > time())) {
                return $this->replyToChat(t('К сожалению, вы не можете писать в этот чат'));
            }

            $event_result = Manager::fire('telegram.command.preExecute', [
                'command' => $this,
            ]);

            if ($event_result->getEvent()->isStopped()) {
                return Request::emptyResponse();
            }

            $result = parent::preExecute();

            $event_result = Manager::fire('telegram.command.afterExecute', [
                'command' => $this,
                'result' => $result
            ]);

            return $event_result->getResult()['result'];
        } catch (\Throwable $e) {
            $log->write(t('Ошибка: %0', [$e->getMessage()]), TelegramLog::LEVEL_INFO);
            throw $e;
        }
    }

    /**
     * Сохраняет предыдущий режим, который был передвыполнением данной команды
     *
     * @param string $mode
     */
    public function setPreviousMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Возвращает предыдущий режим, который был передвыполнением данной команды
     *
     * @return string
     */
    public function getPreviousMode()
    {
        return $this->mode;
    }
}