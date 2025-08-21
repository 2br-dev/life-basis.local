<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\RsTelegram;

use Psr\Log\LoggerInterface;
use Telegram\Model\Log\TelegramLog;

/**
 * Класс необходим для логирования исключительно входящих webhooks от Telegram
 */
class TgLogUpdate implements LoggerInterface
{
    protected $logger;

    function __construct()
    {
        $this->logger = TelegramLog::getInstance();
    }

    /**
     * Сохраняет запись в логе уровня emergency
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_ERROR, $message);
    }

    /**
     * Сохраняет запись в логе уровня alert
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_ERROR, $message);
    }

    /**
     * Сохраняет запись в логе уровня critical
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_ERROR, $message);
    }

    /**
     * Сохраняет запись в логе уровня error
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_ERROR, $message);
    }

    /**
     * Сохраняет запись в логе уровня warning
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_ERROR, $message);
    }

    /**
     * Сохраняет запись в логе уровня notice
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_ERROR, $message);
    }

    /**
     * Сохраняет запись в логе уровня info
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_DEBUG, $message);
    }

    /**
     * Сохраняет запись в логе уровня debug
     *
     * @param \Stringable|string $message
     * @param array $context
     */
    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->log(TelegramLog::LEVEL_TG_DEBUG, $message);
    }

    /**
     * Транслирует запись лога в систему ReadyScript
     *
     * @param mixed $level
     * @param \Stringable|string $message
     * @param array $context
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->logger->write($message, TelegramLog::LEVEL_TG_UPDATE);
    }
}