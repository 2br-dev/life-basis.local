<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Platform;

use RS\Helper\Mailer;
use RS\Orm\AbstractObject;
use Support\Model\Log\LogSupport;
use Support\Model\Orm\CrawlerProfile;
use Support\Model\Orm\Support;
use Support\Model\Orm\Topic;

/**
 * Класс описывает тип Тикетов, создаваемых через почтовый сборщик
 */
class PlatformEmail extends AbstractPlatform
{
    const PLATFORM_ID = 'email';

    protected $log;

    function __construct()
    {
        $this->log = LogSupport::getInstance();
    }

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
        return t('Электронная почта');
    }

    /**
     * Обработчик сохранения тикета
     *
     * @param Topic $topic Объект темы переписки
     * @param string $save_flag флаг операции
     *
     * @return void
     */
    public function onSaveTicket(Topic $topic, $save_flag)
    {
        if ($save_flag == AbstractObject::INSERT_FLAG) {
            //Создание нового тикета
            $profile = $this->getCrawlerProfile();
            $mailer = $profile->getMailer();
            $mailer->addCustomHeader('X-Autoreply', 'YES');
            $mailer->addAddress($topic['user_email']);
            $mailer->Subject = t('Вашему вопросу присвоен номер [#%0]', [$topic['number']]);
            $mailer->Body = $this->getCreateTicketMailHtml($profile, $topic);
            if ($mailer->Body) {
                $this->sendAndLog($profile, $mailer, $topic['user_email']);
            }
        }
    }

    /**
     * Отправляет письмо и логирует результат
     *
     * @param CrawlerProfile $profile
     * @param Mailer $mailer
     * @param $email
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    function sendAndLog(CrawlerProfile $profile, Mailer $mailer, $email)
    {
        $result = $mailer->send();

        if ($result) {
            $profile['date_of_last_send'] = date('c');
            $profile->update();

            $this->log->write(t('Отправлено ответное письмо на Email %email с темой %subject', [
                'email' => $email,
                'subject' => $mailer->Subject
            ]), LogSupport::LEVEL_PLATFORM_MAIL);
        } else {
            $this->log->write(t('Не удалось отправить ответное письмо на Email %email с темой %subject. Причина: %error', [
                'email' => $email,
                'subject' => $mailer->Subject,
                'error' => $mailer->ErrorInfo
            ]), LogSupport::LEVEL_PLATFORM_MAIL);
        }

        return $result;
    }


    /**
     * Обработчик сохранения сообщения
     *
     * @param Support $message Объект сообщения
     * @param string $save_flag флаг операции
     *
     * @return void
     */
    public function onSaveMessage(Support $message, $save_flag)
    {
        if ($save_flag == AbstractObject::INSERT_FLAG && $message['is_admin']) {
            //Создание нового сообщения
            $topic = $message->getTopic();
            $profile = $this->getCrawlerProfile();
            $mailer = $profile->getMailer($profile);
            $mailer->addCustomHeader('X-Autoreply', 'YES');
            $mailer->addAddress($topic['user_email']);
            $mailer->Subject = t('[#%0] %1', [$topic['number'], $topic['title']]);
            $mailer->Body = $this->getAnswerTicketMailHtml($profile, $topic, $message);

            foreach($message->getAttachments() as $file) {
                $mailer->addAttachment($file->getServerPath(), $file['name']);
            }

            if ($mailer->Body) {
                $this->sendAndLog($profile, $mailer, $topic['user_email']);
            }
        }
    }

    /**
     * Возвращает готовый HTML уведомления о создании тикета
     *
     * @param CrawlerProfile $profile
     * @param Topic $topic
     * @return string
     */
    public function getCreateTicketMailHtml(CrawlerProfile $profile, Topic $topic)
    {
        return $this->replaceVars($profile, $profile['template_user_create_ticket'], [
            'ticket' => $topic
        ]);
    }

    /**
     * Возвращает Подпись к сообщению
     *
     * @param CrawlerProfile $profile
     * @return string
     */
    public function getSignatureHtml(CrawlerProfile $profile)
    {
        return (string)$profile['template_signature'];
    }

    /**
     * Возвращает HTML уведомления о новом сообщении администратора
     *
     * @param CrawlerProfile $profile
     * @param Topic $topic
     * @param Support $support
     * @return string
     */
    public function getAnswerTicketMailHtml(CrawlerProfile $profile, Topic $topic, Support $support)
    {
        return $this->replaceVars($profile, $profile['template_admin_answer_message'], [
            'ticket' => $topic,
            'message' => $support
        ]);
    }

    /**
     * Возвращает
     *
     * @param string $html
     * @param $extra_orm
     * @return string
     */
    protected function replaceVars($profile, $html, $extra_orm)
    {
        $find = [
            'signature' => $this->getSignatureHtml($profile)
        ];

        foreach($extra_orm as $key => $object) {
            if ($object instanceof AbstractObject) {
                foreach ($object->getProperties() as $property => $value) {
                    if (is_string($object[$property])) {
                        $find[$key . '.' . $property] = $object[$property];
                    }
                }
            } else {
                $find[$key] = $object;
            }
        }

        foreach($find as $search => $replace) {
            $html = str_replace("%{".$search."}", $replace, $html);
        }

        return $html;
    }

    /**
     * Возвращает профиль почтового сборщика
     *
     * @return CrawlerProfile
     */
    public function getCrawlerProfile()
    {
        $id = $this->getPlatformData('crawler_profile_id', 0);
        return new CrawlerProfile($id);
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
        $profile = $this->getCrawlerProfile();
        return [
            [
                'title' => 'Профиль сборщика',
                'value' => $profile['title']
            ]
        ];
    }
}