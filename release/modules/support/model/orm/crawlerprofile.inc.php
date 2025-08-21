<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Orm;

use PhpImap\Mailbox;
use RS\Exception;
use RS\Helper\Mailer;
use RS\Helper\Tools;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Support\Model\CrawlerProfileApi;

/**
 * Профиль сборщика почты
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название профиля
 * @property string $email Электронная почта
 * @property string $from Поле From(от кого)
 * @property string $server_host Почтовый сервер (Хост)
 * @property string $server_protocol Протокол
 * @property string $server_crypto Шифрование
 * @property string $server_port Порт
 * @property string $server_login Логин
 * @property string $server_password Пароль
 * @property integer $is_enable Включено
 * @property integer $crawl_interval_min Интервал сборки писем
 * @property string $date_of_last_receive Дата последнего успешного получения писем
 * @property integer $send_by_same_smtp Отправлять ответы администратора через SMTP того же сервера, куда письма приходят
 * @property integer $smtp_port Порт SMTP
 * @property string $smtp_crypto_type Тип шифрования SMTP
 * @property string $date_of_last_send Дата последней успешной отправки писем
 * @property string $template_user_create_ticket Уведомление для клиента о создании тикета по инициативе клиента
 * @property string $template_admin_answer_message Уведомление для клиента о новом ответе администратора
 * @property string $template_signature Подпись, которая будет доступна в переменной %{signature}
 * --\--
 */
class CrawlerProfile extends OrmObject
{
    const CRAWL_INTERVAL_NONE = 0;
    const CRAWL_INTERVAL_MIN_1 = 1;
    const CRAWL_INTERVAL_MIN_2 = 2;
    const CRAWL_INTERVAL_MIN_5 = 5;
    const CRAWL_INTERVAL_MIN_60 = 60;
    const CRAWL_INTERVAL_MIN_360 = 360;

    const CRYPTO_NONE = '';
    const CRYPTO_TLS = 'tls';
    const CRYPTO_SSL = 'ssl';

    const PROTOCOL_POP3 = 'pop3';
    const PROTOCOL_IMAP = 'imap';

    protected static $table = 'support_mail_crawler';

    public function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => (new Type\CurrentSite()),
                'title' => (new Type\Varchar)
                    ->setDescription(t('Название профиля')),
                'email' => (new Type\Varchar)
                    ->setDescription(t('Электронная почта')),
                'from' => (new Type\Varchar)
                    ->setDescription(t('Поле From(от кого)'))
                    ->setHint(t('Только текст, без указания Email-адреса')),
                'server_host' => (new Type\Varchar)
                    ->setDescription(t('Почтовый сервер (Хост)')),
                'server_protocol' => (new Type\Varchar)
                    ->setDescription(t('Протокол'))
                    ->setListFromArray([
                        self::PROTOCOL_IMAP => 'IMAP',
                        self::PROTOCOL_POP3 => 'POP3'
                    ]),
                'server_crypto' => (new Type\Varchar)
                    ->setDescription(t('Шифрование'))
                    ->setListFromArray([
                        self::CRYPTO_NONE => t('По умолчанию'),
                        self::CRYPTO_SSL => 'SSL',
                        self::CRYPTO_TLS => 'TLS'
                    ]),
                'server_port' => (new Type\Varchar)
                    ->setDescription(t('Порт'))
                    ->setHint(t('Стандартный порт для IMAP - 143, для POP3 - 110')),
                'server_login' => (new Type\Varchar)
                    ->setDescription(t('Логин')),
                'server_password' => (new Type\Varchar)
                    ->setDescription(t('Пароль'))
                    ->setAttr([
                        'type' => 'password'
                    ]),
                'is_enable' => (new Type\Integer)
                    ->setDescription(t('Включено'))
                    ->setCheckboxView(1, 0),
                'crawl_interval_min' => (new Type\Integer)
                    ->setDescription(t('Интервал сборки писем'))
                    ->setList([__CLASS__, 'getCrawlIntervals']),
                'date_of_last_receive' => (new Type\Datetime())
                    ->setDescription(t('Дата последнего успешного получения писем'))
                    ->setReadOnly(true),
            t('Отправка почты'),
                'send_by_same_smtp' => (new Type\Integer)
                    ->setDescription(t('Отправлять ответы администратора через SMTP того же сервера, куда письма приходят'))
                    ->setHint(t('Если установить данный флаг, то будет использован тот же Хост, логин, пароль, что и у IMAP сервера. Если не устанавливать данный флаг, то письма будут отправляться с помощью основных настроек сайта'))
                    ->setCheckboxView(1,0),
                'smtp_port' => (new Type\Integer)
                    ->setDescription(t('Порт SMTP')),
                'smtp_crypto_type' => (new Type\Enum([
                        self::CRYPTO_NONE,
                        self::CRYPTO_TLS,
                        self::CRYPTO_SSL
                    ]))
                    ->setDescription(t('Тип шифрования SMTP'))
                    ->setListFromArray([
                        self::CRYPTO_NONE => t('Без шифрования'),
                        self::CRYPTO_TLS => t('TLS'),
                        self::CRYPTO_SSL => t('SSL'),
                    ]),
                'date_of_last_send' => (new Type\Datetime())
                    ->setDescription(t('Дата последней успешной отправки писем'))
                    ->setReadOnly(true),
            t('Шаблоны писем'),
                'template_user_create_ticket' => (new Type\Richtext)
                    ->setDescription(t('Уведомление для клиента о создании тикета по инициативе клиента'))
                    ->setHint(t('Можно использовать переменные:<br> %{ticket.title} - Заголовок тикета<br>%{ticket.number} - Номер тикета<br>%{signature} - Подпись из шаблона'))
                    ->setTemplate('%support%/admin/config/load_default.tpl'),
                'template_admin_answer_message' => (new Type\Richtext)
                    ->setDescription(t('Уведомление для клиента о новом ответе администратора'))
                    ->setHint(t('Можно использовать переменные:<br> %{ticket.title} - Заголовок тикета<br>%{ticket.number} - Номер тикета<br>%{signature} - Подпись из шаблона<br>%{message.message} - Сообщение от администратора'))
                    ->setTemplate('%support%/admin/config/load_default.tpl'),
                'template_signature' => (new Type\Richtext)
                    ->setDescription(t('Подпись, которая будет доступна в переменной <nobr>%{signature}</nobr>'))
                    ->setTemplate('%support%/admin/config/load_default.tpl'),
        ]);
    }

    /**
     * Возвращает допустимые интервалы сборки писем
     *
     * @return []
     */
    public static function getCrawlIntervals()
    {
        return [
            self::CRAWL_INTERVAL_NONE => t('Не проверять'),
            self::CRAWL_INTERVAL_MIN_1 => t('Каждую минуту'),
            self::CRAWL_INTERVAL_MIN_2 => t('Каждые 2 минуты'),
            self::CRAWL_INTERVAL_MIN_5 => t('Каждые 5 минут'),
            self::CRAWL_INTERVAL_MIN_60 => t('Каждый час'),
            self::CRAWL_INTERVAL_MIN_360 => t('Каждыt 6 часов'),
        ];
    }

    /**
     * Возвращает строку подключения для протокола IMAP
     *
     * @return string
     */
    public function getImapPath()
    {
        $crypto = $this['server_crypto'] ? $this['server_crypto'].'/' : '';
        $imap_path = "{{$this['server_host']}:{$this['server_port']}".
            "/{$this['server_protocol']}/{$crypto}novalidate-cert}INBOX";

        return $imap_path;
    }

    /**
     * Проверяет прием почты
     */
    public function checkReceive()
    {
        if ($modules = CrawlerProfileApi::getUnexistsModules()) {
            return $this->addError(t('Отсутствуют модули для PHP: %0', [implode( ', ', $modules)]));
        }

        try {
            require(__DIR__.'/../../vendor/autoload.php');
            $mailbox = new Mailbox(
                $this->getImapPath(),
                (string)Tools::unEntityString($this['server_login']),
                (string)Tools::unEntityString($this['server_password'])
            );
            $mailbox->setTimeouts(1, [IMAP_OPENTIMEOUT]);
            $mailbox->getImapStream(true);
            return true;

        } catch (\Exception $e) {
            return $this->addError($e->getMessage());
        }
    }

    /**
     * Проверяет отправку почты
     */
    public function checkSend()
    {
        if ($modules = CrawlerProfileApi::getUnexistsModules()) {
            return $this->addError(t('Отсутствуют модули для PHP: %0', [implode(', ', $modules)]));
        }

        $mailer = $this->getMailer();
        $mailer->addAddress('test@example.com');
        $mailer->Subject = 'Test';
        $mailer->Body = 'Test';
        if ($mailer->send()) {
            return true;
        } else {
            return $this->addError($mailer->ErrorInfo);
        }
    }

    /**
     * Возвращает объект почтового сервиса с учетом настроек профиля
     *
     * @return Mailer
     */
    public function getMailer()
    {
        $mailer = new Mailer();
        $mailer->setFrom($this['email'], Tools::unEntityString($this['from']));
        $mailer->clearReplyTos();
        $mailer->addReplyTo($this['email'], Tools::unEntityString($this['from']));

        if ($this['send_by_same_smtp']) {
            $mailer->isSMTP();
            $mailer->Host = $this['server_host'];
            $mailer->Port = $this['smtp_port'];
            $mailer->SMTPSecure = $this['smtp_crypto'];
            $mailer->SMTPAuth = true;
            $mailer->Username = Tools::unEntityString($this['server_login']);
            $mailer->Password = Tools::unEntityString($this['server_password']);

            $mailer->DKIM_selector = '';
            $mailer->DKIM_identity = '';
            $mailer->DKIM_passphrase = '';
            $mailer->DKIM_domain = '';
        }

        return $mailer;
    }
}