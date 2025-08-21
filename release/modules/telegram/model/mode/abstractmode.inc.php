<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Mode;

use RS\Event\Manager as EventManager;
use RS\Exception;
use Telegram\Model\Commands\AbstractSystemCommand;
use Telegram\Model\Log\TelegramLog;
use Telegram\Model\Orm\TelegramChat;

/**
 * Базовый класс для всех режимов работы телеграм бота.
 * Телеграм-бот всегда работает в рамках какого-то режима. По умолчанию - это default
 * Именно у модуля режима вызываются обработчики
 */
abstract class AbstractMode
{
    private $chat;
    /**
     * @var $log TelegramLog
     */
    protected $log;

    function __construct()
    {
        $this->log = TelegramLog::getInstance();
    }

    /**
     * Устанавливает объект чата
     *
     * @param TelegramChat $chat
     */
    public function setTelegramChat(TelegramChat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Возвращает объект чата
     *
     * @return TelegramChat
     * @throws Exception
     */
    public function getTelegramChat()
    {
        if (!isset($this->chat)) {
            throw new Exception(t('Режим работы телеграм чата не инициализирован.'));
        }

        return $this->chat;
    }

    /**
     * Обработчик, вызываемый при входе в данный режим
     * @param AbstractSystemCommand $command Объект обработчика команды от Телеграм
     *
     * @return void
     */
    public function onEnterMode($command)
    {}

    /**
     * Обработчик, вызываемый при выходе из данного режима
     * @param AbstractSystemCommand $command Объект обработчика команды от Телеграм
     *
     * @return void
     */
    public function onLeaveMode($command)
    {}

    /**
     * Обработчик, вызываемый при получении нового сообщения от Telegram
     *
     * @param AbstractSystemCommand $command Объект обработчика команды от Телеграм
     *
     * @return void
     */
    public function onMessage($command)
    {}

    /**
     * Возвращает ID режима работы
     *
     * @return string
     */
    abstract public static function getId();

    /**
     * Возвращает название режима работы
     *
     * @return string
     */
    abstract public static function getTitle();

    /**
     * Возвращает режим работы по его идентификатору
     *
     * @param $mode_id
     * @param TelegramChat $chat
     * @param bool $use_default
     * @return AbstractMode
     */
    public static function getById($mode_id, TelegramChat $chat, $use_default = true)
    {
        $mode = null;
        $mode_list = self::getModes();
        if (isset($mode_list[$mode_id])) {
            $mode = clone $mode_list[$mode_id];
        } elseif ($use_default) {
            $mode = new DefaultMode();
        }

        if ($mode) {
            $mode->setTelegramChat($chat);
        }

        return $mode;
    }

    /**
     * Возвращает набор возможных режимов
     *
     * @param bool $cache Использовать статический кэш
     * @return AbstractMode[]|null
     */
    public static function getModes($cache = true)
    {
        static $result;

        if (!$cache || $result === null) {
            $result = [];
            $event_result = EventManager::fire('telegram.getmodes', []);
            foreach($event_result->getResult() as $item) {
                $result[$item->getId()] = $item;
            }
        }
        return $result;
    }

    /**
     * Возвращает набор возможных названий режимов
     *
     * @param array $first Массив, который будет добавлен в начало списка
     * @return array
     */
    public static function getModesTitles($first = [])
    {
        $result = [];
        foreach(self::getModes() as $key => $object) {
            $result[$key] = $object->getTitle();
        }

        return $first + $result;
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
        return [];
    }

    /**
     * Возвращает первую в списке текстовый идентификатор данной команды
     *
     * @return mixed
     */
    public static function getFirstTextCommand()
    {
        $commands = static::getTextCommands();
        return reset($commands);
    }
}