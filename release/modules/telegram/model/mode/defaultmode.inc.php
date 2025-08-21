<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Mode;

use Files\Model\FileApi;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use RS\Exception;
use RS\Helper\Tools;
use RS\Orm\Request as OrmRequest;
use Support\Model\FilesType\SupportFiles;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;
use Telegram\Model\Commands\AbstractSystemCommand;
use Telegram\Model\Orm\Profile;
use Telegram\Model\Orm\TelegramUser;
use Telegram\Model\Platform\PlatformTelegram;

/**
 * Режим работы по умолчанию. Сообщения попадают в поддержку по умолчанию (если таковая опция включена)
 */
class DefaultMode extends AbstractMode
{
    const SUPPORT_EXTERNAL_ID_PREFIX = 'tg-';
    /**
     * @var $command Command
     */
    protected $command;

    /**
     * Обработчик, вызываемый при входе в данный режим
     * @param AbstractSystemCommand $command Объект обработчика команды от Телеграм
     *
     * @return void
     */
    public function onEnterMode($command)
    {
        //Если вход в данный режим был вызван командой от пользователя,
        //то расскажем про возможности режима
        $text = $command->getMessage()->getText();
        if ($text == '/'.static::getId()) {
            $chat = $this->getTelegramChat();
            $profile = $chat->getProfile();

            if ($profile['process_message_in_support']) {
                $command->replyToChat(t('Если будут вопросы, пишите нам. Любое ваше сообщение будет направлено в службу поддержки.'));
            } else {
                $command->replyToChat(t('К сожалению, поддержка в этом чате отключена.'));
            }
        }
    }

    /**
     * Обработчик входящих сообщений от Telegram
     *
     * @param Command $command Объект обработчика команды от Телеграм
     * @throws Exception
     * @return void
     */
    public function onMessage($command)
    {
        $this->command = $command;
        $message = $this->command->getMessage();
        if (!$message) {
            $message = $this->command->getEditedMessage();
        }

        $chat = $this->getTelegramChat();
        $profile = $chat->getProfile();

        if ($profile['process_message_in_support']) {

            if ($profile['allow_write_only_authorized_users'] && !$this->command->tg_user->getRsUser()->id) {
                $this->command->replyToChat(t('Сейчас мы не получаем ваши сообщения. Пожалуйста, авторизуйтесь, чтобы отправлять нам сообщения (/login)'), [
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true
                ]);
                return;
            }

            try {
                $topic = $this->findOrCreateTopic($this->command->tg_user, $profile, $message);
                $this->addMessage($this->command->tg_user, $profile, $topic, $message);
            } catch (Exception $e) {
                $this->log->write(t('Ошибка обработки сообщения %id. Причина: %reason', [
                    'id' => $message->getMessageId(),
                    'reason' => $e->getMessage()
                ]));

                throw $e; //Бросаем дальше ошибку, чтобы остановить принятие сообщений до устранения причины
            }
        } else {
            $command->replyToChat(t('К сожалению, поддержка в этом чате отключена, свяжитесь с нами по другим каналам.'));
        }
    }

    /**
     * Находит существующий или создает тикет в поддержке
     *
     * @param Profile $profile
     * @param Message $message
     * @return Topic
     */
    protected function findOrCreateTopic($tg_user, $profile, $message)
    {
        $topic = OrmRequest::make()
            ->from(new Topic)
            ->where([
                'site_id' => $profile['site_id'],
                'platform' => PlatformTelegram::PLATFORM_ID,
                'external_id' => self::SUPPORT_EXTERNAL_ID_PREFIX.$message->getChat()->getId()
            ])
            ->whereIn('status', [Topic::STATUS_OPEN, Topic::STATUS_ANSWERED])
            ->object();

        if (!$topic) {
           $topic = $this->createTopic($tg_user, $profile, $message);
        }

        return $topic;
    }

    /**
     * Создает тикет в поддержке
     *
     * @param TelegramUser $tg_user
     * @param Profile $profile
     * @param Message $message
     * @return Topic Topic
     * @throws Exception
     */
    protected function createTopic($tg_user, $profile, $message)
    {
        $text = $message->getText() ?? $message->getCaption();

        $topic = new Topic();
        $topic['site_id'] = $profile['site_id'];
        $topic['platform'] = PlatformTelegram::PLATFORM_ID;
        $topic['external_id'] = self::SUPPORT_EXTERNAL_ID_PREFIX.$message->getChat()->getId();
        $topic['user_name'] = Tools::toEntityString($tg_user['first_name'].'('.$tg_user['username'].')');
        $topic['user_id'] = $tg_user['user_id'];
        $topic['just_created'] = true;
        $topic->setPlatformData([
            'telegram_profile_id' => $profile['id'],
            'telegram_chat_id' => $this->getTelegramChat()->chat_id,
            'telegram_user_id' => $tg_user['external_id'],
            'telegram_message_id' => $message->getMessageId()
        ]);

        if ($text != '') {
            $teaser = Tools::toEntityString(Tools::teaser($text, 500, true));
        } else {
            $teaser = t('Без темы');
        }

        $topic['title'] = t('Telegram: %teaser', [
            'teaser' => $teaser
        ]);
        if (!$topic->insert()) {
            throw new Exception(t('Не удалось создать тикет в поддержке. Причина: %0', [$topic->getErrorsStr()]));
        }

        return $topic;
    }

    /**
     * Добавляет сообщение в переписку или добавляет файл в сообщение.
     *
     * @param TelegramUser $tg_user
     * @param Profile $profile
     * @param Topic $topic
     * @param Message $message
     */
    protected function addMessage($tg_user, $profile, $topic, $message)
    {
        $text = $message->getText();
        if (!$text) {
            $text = $message->getCaption();
        }

        $media_group_id = $message->getMediaGroupId();
        if ($media_group_id && $text == '') {
            $support = Support::loadByWhere([
                'mediagroup_id' => $media_group_id
            ]);
        } else {
            $support = new Support();
        }

        if ($media_group_id && $support['id']) {
            //Добавляем ресурсы к существующему сообщению
            $this->downloadAttachments($profile, $message, $support['id']);
        } else {
            //Создаем или обновляем сообщение
            $support = Support::loadByWhere([
                'external_id' => self::SUPPORT_EXTERNAL_ID_PREFIX.$message->getMessageId()
            ]);

            $support['user_id'] = $tg_user['user_id'];
            $support['message'] = Tools::toEntityString($text);
            $support['is_admin'] = 0;
            $support['topic_id'] = $topic['id'];
            $support['message_type'] = Support::TYPE_USER_MESSAGE;
            $support['external_id'] = self::SUPPORT_EXTERNAL_ID_PREFIX.$message->getMessageId();
            $support['mediagroup_id'] = $message->getMediaGroupId();
            $support['attachments'] = $this->downloadAttachments($profile, $message);

            if ($topic['just_created']) {
                $support['is_first_topic_message'] = true;
            } else {
                $support['dont_send_admin_notice'] = true;
            }

            if ($support['id']) {
                $support['updated'] = date('c');
                $result = $support->update();
            } else {
                $result = $support->insert();
            }

            if (!$result) {
                throw new Exception(t('Не удалось создать сообщение в поддержке. Причина: %0', [$support->getErrorsStr()]));
            }
        }
    }

    /**
     * Возвращает список вложений
     *
     * @param Profile $profile
     * @param Message $message
     * @param int $link_id
     *
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function downloadAttachments($profile, $message, $link_id = 0)
    {
        $result = [];
        $download_path = $profile->getTelegramBot()->getDownloadPath();
        $message_type = $message->getType();

        if (in_array($message_type, ['audio', 'document', 'photo', 'video', 'voice'], true)) {
            $doc = $message->{'get' . ucfirst($message_type)}();

            // For photos, get the best quality!
            if ($message_type === 'photo') {
                $doc = end($doc);
            }

            $file_id = $doc->getFileId();
            $file    = Request::getFile(['file_id' => $file_id]);

            if ($file->isOk() && Request::downloadFile($file->getResult())) {
                $filepath = $download_path . '/' . $file->getResult()->getFilePath();

                if (isset($doc->mime_type)) {
                    $mime = $doc->mime_type;
                } else {
                    $mime = mime_content_type($filepath) ?: 'application/octet-stream';
                }

                $file_api = new FileApi();
                $file = $file_api->uploadFromUrl($filepath, new SupportFiles(), $link_id, basename($filepath),
                    FileApi::UPLOAD_TYPE_COPY, [
                        'mime' => $mime
                    ]);

                if (!$file) {
                    throw new Exception(t('Не удалось переместить файл %name. Причина: %reason', [
                        'name' => $filepath,
                        'reason' => $file_api->getErrorsStr()
                    ]));
                } else {
                    $result[] = $file['uniq'];
                }
                unlink($filepath); //Удаляем файл сразу после загрузки
            } else {
                throw new Exception(t('Не удалось скачать файл, ID: %id', [
                    'id' => $file_id
                ]));
            }
        }

        return $result;
    }

    /**
     * Возвращает ID режима работы
     *
     * @return string
     */
    public static function getId()
    {
        return 'default';
    }

    /**
     * Возвращает название режима работы
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('По умолчанию');
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
        return [t('👨 Служба поддержки')];
    }
}