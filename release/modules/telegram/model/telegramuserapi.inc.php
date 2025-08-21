<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use RS\Exception;
use RS\Helper\Tools;
use RS\Module\AbstractModel\EntityList;
use RS\Site\Manager;
use Telegram\Model\Orm\Profile;
use Telegram\Model\Orm\TelegramChat;
use Telegram\Model\Orm\TelegramUser;
use Telegram\Model\RsTelegram\TgBot;

/**
 * PHP API для работы с выборками пользователей Telegram
 */
class TelegramUserApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\TelegramUser(), [
            'idField' => 'external_id'
        ]);
    }

    /**
     * Отправляет сообщение пользователю в Telegram,
     * если данный пользователь вступил в чат-бот и авторизовался в нем.
     *
     * @param integer $user_id ID пользователя в ReadyScript
     * @param string $message_html Текст сообщения в формате HTML
     * @param integer|null $profile_id ID профиля чат-бота Телеграм. Если null, то будет использован профиль по умолчанию.
     * @return ServerResponse
     * @throws Exception
     */
    public static function sendMessageToUser($user_id, $message_html, $profile_id = null)
    {
        $tg_user = TelegramUser::loadByWhere([
            'user_id' => $user_id
        ]);

        if (!$tg_user['external_id']) {
            throw new Exception(t('Пользователь Telegram не найден'));
        }

        if ($profile_id === null) {
            $profile = Profile::loadByWhere([
                'site_id' => Manager::getSiteId(),
                'is_default' => 1
            ]);
        } else {
            $profile = new Profile($profile_id);
        }

        if (!$profile['id']) {
            throw new Exception(t('Не найден профиль работы с Telegram ботом'));
        }

        $chat = TelegramChat::loadByWhere([
            'profile_id' => $profile['id'],
            'telegram_user_id' => $tg_user['external_id']
        ]);

        if (!$chat['chat_id']) {
            throw new Exception(t('Не найден чат с пользователем Telegram'));
        }

        $bot = $profile->getTelegramBot();
        Request::initialize($bot);

        return Request::sendMessage([
            'chat_id' => $chat['chat_id'],
            'text' => TgBot::htmlToMarkdown(Tools::unEntityString($message_html)),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true
        ]);
    }
}