<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\RsTelegram;

require_once(__DIR__.'/../../vendor/autoload.php');

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use RS\File\Tools;
use Telegram\Model\Orm\Profile;

/**
 * Класс, обеспечивающий взаимодействие с Telegram.
 * Обертка над TelegramBot\Telegram
 */
class TgBot extends Telegram
{
    static $instance = [];
    public $profile;

    /**
     * Telegram constructor.
     *
     * @param string $api_key
     * @param string $bot_username
     *
     * @throws TelegramException
     */
    public function __construct(string $api_key, string $bot_username = '')
    {
        \Longman\TelegramBot\TelegramLog::initialize(new TgLog(), new TgLogUpdate());
        \Longman\TelegramBot\TelegramLog::$always_log_request_and_response = true;
        \Longman\TelegramBot\TelegramLog::$remove_bot_token = false;

        parent::__construct($api_key, $bot_username);
    }

    /**
     * Возвращает объет для взаимодействия с телеграмом на основе профиля
     *
     * @param Profile $profile
     * @return TgBot
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public static function makeFromProfile(Profile $profile)
    {
        $key = $profile['id'].$profile['api_token'].$profile['bot_name'];
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = new static($profile['api_token'], $profile['bot_name']);
        }
        /**
         * @var $self TgBot
         */
        $self = self::$instance[$key];
        $self->useGetUpdatesWithoutDatabase();
        $self->profile = $profile;

        $download_dir = \Setup::$PATH.\Setup::$STORAGE_DIR.'/telegram/';
        Tools::makePrivateDir($download_dir);
        $self->setDownloadPath($download_dir);
        $self->initRsCommands();

        return $self;
    }

    /**
     * Инициализирует обработчики команд ReadyScript
     */
    public function initRsCommands()
    {
        $this->commands_paths = [];

        $commands_folder = __DIR__.'/../commands';
        $files = scandir($commands_folder);
        foreach($files as $file) {
            if (preg_match('/^(.*)\.inc\.php$/', $file, $match)) {
                $class_name = $match[1];
                $full_class_name = '\\Telegram\\Model\\Commands\\'.$class_name;
                if (!in_array($class_name, ['abstractsystemcommand']) && class_exists($full_class_name)) {
                    $this->addCommandClass($full_class_name);
                }
            }
        }
    }

    /**
     * Конвертирует html текст в markdown формат только с учетом поддерживаемых инструкций Telegram
     *
     * @param $html
     * @return mixed|string|string[]|null
     */
    public static function htmlToMarkdown($html)
    {
        $message_text = htmlspecialchars_decode($html);

        //Специальная подготовка текста для Telegram. HTML -> Markdown с учетом поддерживаемого синтаксиса.
        $message_text = str_replace(['_', '*', '`', '['], ['\\_', '\\*', '\\`', '\\['], $message_text);
        $message_text = str_replace(['<br>', '<br />','<p>', '</p>'], ["\n", "\n", "\n", ""], $message_text);
        $message_text = str_replace(['<strong>','</strong>'], ["*", "*"], $message_text);
        $message_text = str_replace(['<em>','</em>'], ["_", "_"], $message_text);
        $message_text = preg_replace_callback('/\<a href="([^">]*?)".*?\>(.*?)\<\/a>/uim', function($match) {
            $text = $match[2];
            $href = str_replace('\\_', '_', $match[1]);
            return "[{$text}]({$href})";
        }, $message_text);
        $message_text = strip_tags($message_text);
        $message_text = html_entity_decode($message_text);

        return $message_text;
    }

}