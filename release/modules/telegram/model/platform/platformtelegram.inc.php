<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Platform;

use GuzzleHttp\Psr7\Stream;
use Longman\TelegramBot\Entities\InputMedia\InputMediaDocument;
use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;
use Longman\TelegramBot\Request;
use RS\Helper\Tools;
use RS\Orm\AbstractObject;
use RS\View\Engine;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;
use Support\Model\Platform\AbstractPlatform;
use Telegram\Model\Orm\Profile;
use Telegram\Model\Orm\TelegramUser;
use Telegram\Model\RsTelegram\TgBot;

/**
 * Класс описывает платформу Телеграм для поддержки (модуля support)
 */
class PlatformTelegram extends AbstractPlatform
{
    const PLATFORM_ID = 'telegram';
    protected $user_info_tpl = '%telegram%/admin/topic_view_user.tpl';

    /**
     * Возвращает идентификатор платформы
     *
     * @return string
     */
    function getId()
    {
        return self::PLATFORM_ID;
    }

    /**
     * Возвращает название платформы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Telegram');
    }

    /**
     * Обработчик сохранения тикета
     *
     * @param Topic $topic Объект темы переписки
     * @param string $save_flag флаг операции
     * RS\Orm\AbstractObject::INSERT_FLAG,
     * RS\Orm\AbstractObject::UPDATE_FLAG,
     * RS\Orm\AbstractObject::REPLACE_FLAG
     *
     * @return void
     */
    public function onSaveTicket(Topic $topic, $save_flag)
    {
        $profile_id = $topic->getPlatformData('telegram_profile_id');
        $tg_profile = new Profile($profile_id);

        if ($save_flag == AbstractObject::INSERT_FLAG && $tg_profile['support_notice_ticket_create']) {
            $this->sendTemplate($topic, '%telegram%/admin/support/to_user_ticket_created.tpl', [
                'topic' => $topic
            ]);
        }

        if ($save_flag == AbstractObject::UPDATE_FLAG && $tg_profile['support_notice_ticket_close']) {
            if ($topic['external_id'] && $topic['status'] == Topic::STATUS_CLOSED
                && $topic->before['status'] != $topic['status'])
            {
                $this->sendTemplate($topic, '%telegram%/admin/support/to_user_ticket_closed.tpl', [
                    'topic' => $topic
                ]);
            }
        }

        if ($topic['status'] == Topic::STATUS_CLOSED) {
            //В системе всегда должен находить только один
            // незакрытый тикет с external_id от телеграм, чтобы в него писать следующее сообщение
            \RS\Orm\Request::make()
                ->update($topic)
                ->set(['external_id' => null])
                ->where(['id' => $topic['id']])
                ->exec();
        }
    }

    /**
     * Отправляет в телеграм уведомление клиенту (не фиксируется в поддержке)
     *
     * @param Topic $topic
     * @param $template
     * @param $template_vars
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws \SmartyException
     */
    public function sendTemplate(Topic $topic, $template, $template_vars)
    {
        $profile_id = $topic->getPlatformData('telegram_profile_id');
        $chat_id = $topic->getPlatformData('telegram_chat_id');
        $tg_profile = new Profile($profile_id);

        if ($tg_profile['id'] && $chat_id) {
            $bot = $tg_profile->getTelegramBot();

            $view = new Engine();
            $view->assign($template_vars);
            $text = $view->fetch($template);

            Request::initialize($bot);
            Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => TgBot::htmlToMarkdown($text),
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true
            ]);
        }
    }

    /**
     * Обработчик сохранения сообщения.
     * Отправляет сообщение пользователю о том, что администратор ответил в тикете
     *
     * @param Support $message Объект сообщения
     * @param string $save_flag флаг операции
     *
     * @return void
     */
    public function onSaveMessage(Support $message, $save_flag)
    {
        if ($message['is_admin'] && $save_flag == AbstractObject::INSERT_FLAG) {
            $this->sendAnswerToUser($message);
        }

        if ($save_flag == AbstractObject::UPDATE_FLAG
            && $message['external_id']
            && $message->before['message'] != $message['message']) {
            $this->sendEditAnswerToUser($message);
        }

        if ($save_flag == AbstractObject::INSERT_FLAG && !$message['is_admin']
            && $this->isNeedAutoAnswer($message) && $message['external_id']) {
            $this->sendTemplate($message->getTopic(), '%telegram%/admin/support/to_user_auto_answer.tpl', [
                'topic' => $message->getTopic()
            ]);
        }
    }

    /**
     * Возвращает true, если нужно отправить автоматический ответ после N неотвеченных сообщений пользователя
     *
     * @return bool
     */
    protected function isNeedAutoAnswer($message)
    {
        $profile_id = $message->getTopic()->getPlatformData('telegram_profile_id');
        $profile = new Profile($profile_id);
        if ($profile['id']) {
            $limit = $profile['auto_answer_after_messages'];

            if ($limit > 0) {
                $messages = \RS\Orm\Request::make()
                    ->select('is_admin')
                    ->from(new Support())
                    ->where([
                        'topic_id' => $message['topic_id'],
                    ])
                    ->orderby('id DESC')
                    ->exec()
                    ->fetchAll();

                $last_user_message_count = 0;
                foreach ($messages as $one) {
                    if ($one['is_admin']) {
                        break;
                    }
                    $last_user_message_count++;
                }

                if ($last_user_message_count % $limit == 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Отправляет корректировку сообщения в Telegram
     *
     * @param $message
     */
    public function sendEditAnswerToUser($message)
    {
        $topic = $message->getTopic();
        $profile_id = $topic->getPlatformData('telegram_profile_id');
        $chat_id = $topic->getPlatformData('telegram_chat_id');
        $tg_profile = new Profile($profile_id);

        if ($tg_profile['id'] && $chat_id) {

            $bot = $tg_profile->getTelegramBot();

            Request::initialize($bot);
            Request::editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $message['external_id'],
                'text' => TgBot::htmlToMarkdown(Tools::unEntityString($message->getMessage())),
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true
            ]);
        }
    }

    /**
     * Отправляет сообщение клиенту в Telegram
     *
     * @param Support $message
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendAnswerToUser($message)
    {
        $topic = $message->getTopic();
        $profile_id = $topic->getPlatformData('telegram_profile_id');
        $chat_id = $topic->getPlatformData('telegram_chat_id');
        $tg_profile = new Profile($profile_id);

        if ($tg_profile['id'] && $chat_id) {

            $bot = $tg_profile->getTelegramBot();

            Request::initialize($bot);
            $result = Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => TgBot::htmlToMarkdown(Tools::unEntityString($message->getMessage())),
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true
            ]);

            if ($result->isOk()) {
                //Ставим отметку, что сообщение успешно ушло в telegram
                $message['is_delivered'] = 1;
                $message['external_id'] = $result->getResult()->getMessageId();
                $message->update();
            }

            $image_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            $media = [];
            foreach($message->getAttachments() as $file) {
                if (in_array($file['mime'], $image_mimes)) {
                    $media[] = new InputMediaPhoto(['media' => new Stream(Request::encodeFile($file->getServerPath()), [
                        'metadata' => [
                            'uri' => $file['name']
                        ]
                    ])]);
                } else {
                    $media[] = new InputMediaDocument(['media' => new Stream(Request::encodeFile($file->getServerPath()), [
                        'metadata' => [
                            'uri' => $file['name']
                        ]
                    ])]);
                }
            }

            if ($media) {
                Request::sendMediaGroup([
                    'chat_id' => $chat_id,
                    'media' => $media
                ]);
            }
        }
    }

    /**
     * Возвращает Набор публичных данных, которые необходимо отобразить при просмотре
     * тикета в административной панели
     *
     * @return array
     * [
     *   [
     *      'title' => 'Параметр',
     *      'value' => 'Значение'
     *   ]
     * ]
     */
    public function getPublicData()
    {
        $result = [];

        $profile_id = $this->getPlatformData('telegram_profile_id');
        $tg_profile = new Profile($profile_id);
        if ($tg_profile) {
            $result[] = [
                'title' => t('Telegram бот'),
                'value' => $tg_profile['bot_name']
            ];
        }

        if ($topic = $this->getTopic()) {
            if (!$topic['external_id'] && $topic['status'] != Topic::STATUS_CLOSED) {
                $result[] = [
                    'title' => t('Информация'),
                    'value' => t('Так как данный тикет был уже ранее закрыт, клиент более не сможет отвечать в рамках данного тикета через Telegram')
                ];
            }
        }

        return $result;
    }

    /**
     * Возвращает объект пользователя Telegram
     *
     * @return TelegramUser
     */
    public function getTelegramUser()
    {
        return TelegramUser::loadByWhere([
            'external_id' => $this->getPlatformData('telegram_user_id')
        ]);
    }

    /**
     * Возвращает тип ссылки, который необходимо вернуть в визуальном редакторе для данного тикета
     *
     * @return string
     */
    public function getTinyLinkType()
    {
        $profile_id = $this->getPlatformData('telegram_profile_id');
        $tg_profile = new Profile($profile_id);

        //Предусмотрено для модуля "Магазин в Telegram"
        if ($tg_profile['enable_shop'] && $tg_profile['app_name']) {
            return 'telegram-'.$tg_profile['uniq'];
        } else {
            return static::TINY_LINK_TYPE_SITE;
        }
    }
}