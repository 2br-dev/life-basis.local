<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail;

use Files\Model\FileApi;
use PhpImap\IncomingMail;
use RS\Config\Loader;
use RS\Exception;
use RS\Helper\Tools;
use RS\Module\AbstractModel\BaseModel;
use RS\Orm\Request;
use Support\Model\FilesType\SupportFiles;
use Support\Model\Log\LogSupport;
use Support\Model\Mail\MsTnef\File as MsTnefFile;
use Support\Model\Orm\CrawlerProfile;
use PhpImap\Mailbox;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;
use Support\Model\Platform\PlatformEmail;
use Users\Model\Orm\User;

/**
 * Класс отвечает за загрузку тикетов из электронной почты
 */
class ImapCrawler extends BaseModel
{
    /**
     * @var $profile CrawlerProfile
     */
    protected $profile;
    protected $log;
    protected $config;

    public function __construct(CrawlerProfile $profile)
    {
        require(__DIR__.'/../../vendor/autoload.php');

        $this->profile = $profile;
        $this->log = LogSupport::getInstance();
        $this->config = Loader::byModule($this);
    }

    /**
     * Проверяет новые письма в почте и создает на их основе
     * новые тикеты или дополняет существующие
     *
     * @return bool|integer
     */
    public function fetchMail()
    {
        $this->log->write(t('Начало загрузки почты %email для профиля %title (%id)', [
            'email' => $this->profile['email'],
            'title' => $this->profile['title'],
            'id' => $this->profile['id']
        ]));

        try {
            $mailbox = $this->initMailbox();
            $result = $this->fetchLetters($mailbox);
            $mailbox->disconnect();

            $this->profile['date_of_last_receive'] = date('c');
            $this->profile->update();

            $this->log->write(t('Завершение загрузки писем из почты %email', [
                'email' => $this->profile['email']
            ]));

            return $result;
        } catch (Exception $e) {
            $this->log->write(t('Ошибка во время загрузки почты %email профиля %title (%id): %error', [
                'email' => $this->profile['email'],
                'title' => $this->profile['title'],
                'id' => $this->profile['id'],
                'error' => $e->getMessage()
            ]));

            return $this->addError($e->getMessage());
        }
    }

    /**
     * Инициализирует объект связи с почтовым сервером
     *
     * @return Mailbox
     */
    protected function initMailbox()
    {
        try {
            $mailbox = new Mailbox(
                $this->profile->getImapPath(),
                (string)Tools::unEntityString($this->profile['server_login']),
                (string)Tools::unEntityString($this->profile['server_password'])
            );
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $mailbox;
    }

    /**
     * Загружает список писем и создает/дополняет тикеты
     *
     * @param Mailbox $mailbox
     * @return int
     */
    protected function fetchLetters(Mailbox $mailbox)
    {
        $mail_ids = $mailbox->searchMailbox('ALL');
        rsort($mail_ids);

        $this->log->write(t('Найдено %0 писем', [count($mail_ids)]), LogSupport::LEVEL_PLATFORM_MAIL);

        $i = 0;
        foreach ($mail_ids as $mail_id) {
            $email = $mailbox->getMail (
                $mail_id
            );
            $mail_data = $this->parseOneMail($email);
            $internal_error = false;

            if (MailFilter::isHumanMail($email)) {
                if (!$this->isExists($mail_data)) {
                    $topic = $this->recognizeTopic($mail_data);
                    if ($topic) {
                        //Добавляем сообщение в тикет
                        $message = $this->addMessage($topic, $mail_data);
                        if ($message) {
                            $this->log->write(t('Добавлено сообщение к тикету №%number, Email:%from с темой %subject с текстом: %text', [
                                'number' => $message->getTopic()->number,
                                'from' => $mail_data['address'],
                                'subject' => $mail_data['subject'],
                                'text' => Tools::teaser($message['message'], 200)
                            ]), LogSupport::LEVEL_PLATFORM_MAIL);
                        } else {
                            $this->log->write(t('Ошибка при добавлении сообщения Email:%from с темой %subject, по причине: %error', [
                                'from' => $mail_data['address'],
                                'subject' => $mail_data['subject'],
                                'error' => $this->getErrorsStr()
                            ]), LogSupport::LEVEL_PLATFORM_MAIL);
                            $internal_error = true;
                        }
                    } else {
                        //Создаем новый тикет
                        $message = $this->createTicket($mail_data);
                        if ($message) {
                            $this->log->write(t('Создан тикет №%number, Email:%from с темой %subject', [
                                'number' => $message->getTopic()->number,
                                'from' => $mail_data['address'],
                                'subject' => $mail_data['subject']
                            ]), LogSupport::LEVEL_PLATFORM_MAIL);
                        } else {
                            $this->log->write(t('Ошибка при создании тикета Email:%from с темой %subject, по причине: %error', [
                                'from' => $mail_data['address'],
                                'subject' => $mail_data['subject'],
                                'error' => $this->getErrorsStr()
                            ]), LogSupport::LEVEL_PLATFORM_MAIL);
                            $internal_error = true;
                        }
                    }
                } else {
                    $this->log->write(t('Пропускаем письмо, так как письмо с таким MessageID `%id` уже есть в базе', [
                        'id' => $mail_data['id']
                    ]), LogSupport::LEVEL_PLATFORM_MAIL);
                }
                $i++;
                if ($this->config->one_fetch_limit && $i >= $this->config->one_fetch_limit) {
                    $this->log->write(t('Скачано %0 писем. Прерываем загрузку. Остальное будет загружено в следующей итерации запуска', [$i]), LogSupport::LEVEL_PLATFORM_MAIL);
                    return $i;
                }
            } else {
                $this->log->write(t('Пропущено письмо от %from с темой %subject. Причина: это автоматическое письмо', [
                    'from' => $mail_data['address'],
                    'subject' => $mail_data['subject']
                ]), LogSupport::LEVEL_PLATFORM_MAIL);
            }

            if (!$internal_error) {
                $mailbox->deleteMail($mail_id);
                $this->log->write(t('Удалено письмо %from с темой %subject с сервера', [
                    'from' => $mail_data['address'],
                    'subject' => $mail_data['subject']
                ]), LogSupport::LEVEL_PLATFORM_MAIL);
            }
        }

        $this->log->write(t('Скачаны все письма'), LogSupport::LEVEL_PLATFORM_MAIL);
        return true;
    }

    /**
     * Возвращает true, если данное сообщение уже было загружено ранее.
     *
     * @param $mail_data
     * @return bool
     */
    protected function isExists($mail_data)
    {
        if (!$mail_data['id']) {
            return false;
        }

        $exists = Request::make()
            ->from(new Support())
            ->where([
                'site_id' => $this->profile['site_id'],
                'external_id' => $mail_data['id']
            ])->count();

        return $exists > 0;
    }

    /**
     * Парсит одно письмо и возвращает структурированный массив
     *
     * @param IncomingMail $email
     * @return array
     */
    protected function parseOneMail(IncomingMail $email)
    {
        $mail_data = [];

        $mail_data['id'] = Tools::toEntityString(trim((string)$email->messageId, '<>')) ?: null;
        $mail_data['address'] = Tools::toEntityString((string) $email->fromAddress);
        $mail_data['subject'] = Tools::toEntityString((string) $email->subject);
        $mail_data['message'] = Tools::toEntityString(trim($email->textPlain ?: strip_tags($email->textHtml)));
        $mail_data['files'] = [];

        if ($email->hasAttachments()) {
            foreach($email->getAttachments() as $attachment) {
                if ($attachment->subtype == 'MS-TNEF') {
                    //Обработка вложений от Microsoft Outlook, он кодирует все вложения в одном файле winmail.dat
                    $buffer = $attachment->getContents();
                    $file = new MsTnef\Attachment();
                    $file->decodeTnef($buffer);
                    $files = $file->getFiles();
                    foreach($files as $file) {
                        if ($file instanceof MsTnefFile) {
                            $mail_data['files'][] = [
                                'name' => $file->getName(),
                                'mime' => $file->getType(),
                                'content' => $file->getContent()
                            ];
                        }
                    }
                } else {
                    $mail_data['files'][] = [
                        'name' => $attachment->name,
                        'mime' => $attachment->mimeType,
                        'content' => $attachment->getContents()
                    ];
                }
            }
        }

        return $mail_data;
    }

    /**
     * Пытается найти в базе данных существующий тикет по номеру из заголовка письма
     *
     * @param $mail_data
     * @return bool|Topic
     */
    protected function recognizeTopic($mail_data)
    {
        if (preg_match('/\#(\d{'.$this->config['number_mask_digits'].'})/', $mail_data['subject'], $match)) {
            $find_number = $match[1];
            $topic = Topic::loadByWhere([
                'number' => $find_number,
                'user_email' => $mail_data['address']
            ]);
            if ($topic['id']) {
                return $topic;
            }
        }

        return false;
    }

    /**
     * Возвращает объект пользователя по данным письма
     *
     * @param array $mail_data
     * @return User
     */
    protected function getUser($mail_data)
    {
        $email = Tools::toEntityString($mail_data['address']);
        $user = User::loadByWhere([
            'e_mail' => $email
        ]);
        $user['e_mail'] = $email;

        return $user;
    }

    /**
     * Добавляет сообщение в переписку
     *
     * @param $topic
     * @param array $mail_data
     * @return bool|Support
     */
    protected function addMessage($topic, $mail_data)
    {
        $attachments = $this->uploadAttachmentFromMail($mail_data);

        $message = new Support();
        $message['site_id'] = $this->profile['site_id'];
        $message['user_id'] = $this->getUser($mail_data)->id;
        $message['message'] = $mail_data['message'];
        $message['is_admin'] = 0;
        $message['topic_id'] = $topic['id'];
        $message['message_type'] = Support::TYPE_USER_MESSAGE;
        $message['external_id'] = $mail_data['id'];
        if ($attachments) {
            $message['attachments'] = $attachments;
        }
        if ($message->insert()) {
            return $message;
        }

        return $this->addError($message->getErrorsStr());
    }

    /**
     * Возвращает массив идентификаторов загруженных файлов
     *
     * @param array $mail_data
     * @return array
     */
    protected function uploadAttachmentFromMail($mail_data)
    {
        $file_api = new FileApi();
        $file_api->setSiteContext($this->profile['site_id']);
        $attachments = [];
        if ($this->config['allow_attachments']) {
            $type = new SupportFiles();
            if ($this->config['attachment_allow_email_any_extensions']) {
                $type->setAllowedExtensions([]);
            }
            foreach ($mail_data['files'] as $file_data) {
                $file_api->cleanErrors();
                $file = $file_api->uploadFromData($file_data['content'],
                    $type,
                    0,
                    $file_data['name']);

                if ($file) {
                    $this->log->write(t('Загружен файл %0, размер: %1', [$file_data['name'], strlen($file_data['content'])]), LogSupport::LEVEL_PLATFORM_MAIL);
                    $attachments[] = $file['uniq'];
                } else {
                    $this->log->write(t('Не удалось загрузить файл %name, размер: %size. Причина: %error', [
                        'name' => $file_data['name'],
                        'size' => strlen($file_data['content']),
                        'error' => $file_api->getErrorsStr()
                    ]), LogSupport::LEVEL_PLATFORM_MAIL);
                }
            }
        }

        return $attachments;
    }

    /**
     * Создает новый тикет
     *
     * @param array $mail_data
     * @return bool|Support
     */
    protected function createTicket($mail_data)
    {
        $topic = new Topic();
        $topic['title'] = $mail_data['subject'] ?: t('Без темы');
        $topic['platform'] = PlatformEmail::PLATFORM_ID;
        $topic->setPlatformData([
            'crawler_profile_id' => $this->profile['id']
        ]);
        $topic['user_id'] = $this->getUser($mail_data)->id;
        $topic['user_email'] = $this->getUser($mail_data)->e_mail;
        $topic['updated'] = date('c');
        $topic['msgcount'] = 1;
        $topic['newcount'] = 0;

        if ($topic->insert()) {
            return $this->addMessage($topic, $mail_data);
        } else {
            return $this->addError($topic->getErrorsStr());
        }
    }
}