<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type\CurrentSite;
use RS\Orm\Type;
use RS\Router\Manager;
use Telegram\Model\RsTelegram\TgBot;

/**
 * ORM-объект, характеризующий одного Telegram бота
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название телеграм бота
 * @property string $secret_key Секретная часть URL для Webhooks
 * @property string $bot_name Идентификатор(имя) бота, без знака @
 * @property string $api_token API ключ
 * @property integer $is_webhook_enabled Hooks подключены
 * @property integer $is_default Это профиль по-умолчанию
 * @property integer $is_enable Включен
 * @property string $webhook_ssl_certificate SSL Сертификат
 * @property string $uniq Уникальный строковый идентификатор
 * @property string $welcome_message Текст приветствия (после команды /start)
 * @property integer $show_reply_markup Показывать быстрые кнопки (вход, выход,...) под клавиатурой?
 * @property integer $process_message_in_support Обрабатывать сообщения в поддержке
 * @property integer $support_notice_ticket_create Включить уведомление пользователю "Тикет создан"
 * @property integer $support_notice_ticket_close Включить уведомление пользователю "Тикет закрыт"
 * @property integer $auto_answer_after_messages Количество неотвеченных сообщений клиента для автоответа
 * @property integer $allow_write_only_authorized_users Требовать авторизацию перед отправкой сообщений в поддержку
 * --\--
 */
class Profile extends OrmObject
{
    protected static $table = 'telegram_profile';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new CurrentSite(),
                'title' => (new Type\Varchar())
                    ->setDescription(t('Название телеграм бота'))
                    ->setChecker('chkEmpty', t('Придумайте название профиля'))
                    ->setHint(t('Это внутреннее названия профиля. Придумайте любое название.')),
                'secret_key' => (new Type\Varchar())
                    ->setMaxLength(50)
                    ->setVisible(false)
                    ->setUnique(true)
                    ->setDescription(t('Секретная часть URL для Webhooks')),
                'bot_name' => (new Type\Varchar())
                    ->setChecker('chkEmpty', t('Заполните идентификатор бота'))
                    ->setHint(t('Английский идентификатор бота, который вы указали при создании бота в @Botfather'))
                    ->setDescription(t('Идентификатор(имя) бота, без знака @')),
                'api_token' => (new Type\Varchar())
                    ->setDescription(t('API ключ'))
                    ->setHint(t('Получите API-ключ у @Botfather в telegram, выполнив команду /newbot')),
                'is_webhook_enabled' => (new Type\Integer())
                    ->setVisible(false)
                    ->setDescription(t('Hooks подключены')),
                'is_default' => (new Type\Integer())
                    ->setDescription(t('Это профиль по-умолчанию'))
                    ->setHint(t('Профиль по умолчанию может использоваться сторонними модулями с базовыми настройками, без дополнительных настроек'))
                    ->setCheckboxView(1, 0),
                'is_enable' => (new Type\Integer())
                    ->setDescription(t('Включен'))
                    ->setHint(t('Выключенный модуль не получает сообщения от Telegram'))
                    ->setCheckboxView(1, 0),
                'webhook_ssl_certificate' => (new Type\File())
                    ->setDescription(t('SSL Сертификат'))
                    ->setHint(t('Загрузите сюда сертификат, если вы используете самоподписанный сертификат на вашем сайте.')),
                'uniq' => (new Type\Varchar())
                    ->setDescription(t('Уникальный строковый идентификатор'))
                    ->setHint(t('Будет присвоен после сохранения объекта'))
                    ->setReadOnly(true),
                'welcome_message' => (new Type\Richtext())
                    ->setEditorOptions([
                        'tiny_options' => [
                            'plugins'            => ["link", "searchreplace visualblocks code emoticons", "paste"],
                            'toolbar1'           => 'undo | bold italic underline | removeformat | cut copy paste | searchreplace | link unlink code emoticons',
                            'toolbar2'           => '',
                        ]
                    ])
                    ->setDescription(t('Текст приветствия (после команды /start)')),
                'show_reply_markup' => (new Type\Integer())
                    ->setDescription(t('Показывать быстрые кнопки (вход, выход,...) под клавиатурой?'))
                    ->setHint(t('Рекомендуем отключать только в случае, если вы пользуетесь модулем Магазин в Telegram, так как в этом случае авторизовываться можно через WebApp - приложение. Для применения изменений в конкретном чате, нужно будет выполнить команду /start'))
                    ->setDefault(1)
                    ->setCheckboxView(1, 0),
            t('Поддержка'),
                'process_message_in_support' => (new Type\Integer())
                    ->setDescription(t('Обрабатывать сообщения в поддержке'))
                    ->setHint(t('Все сообщения, кроме специальных режимов бота, будут направлены в поддержку'))
                    ->setCheckboxView(1, 0),
                'support_notice_ticket_create' => (new Type\Integer())
                    ->setDescription(t('Включить уведомление пользователю "Тикет создан"'))
                    ->setCheckboxView(1, 0),
                'support_notice_ticket_close' => (new Type\Integer())
                    ->setDescription(t('Включить уведомление пользователю "Тикет закрыт"'))
                    ->setCheckboxView(1, 0),
                'auto_answer_after_messages' =>  (new Type\Integer())
                    ->setDefault(3)
                    ->setDescription(t('Количество неотвеченных сообщений клиента для автоответа'))
                    ->setHint(t('Система отправит автосообщение "Мы получили ваши сообщения, пожалуйста, ожидайте ответа", после N неотвеченных сообщений от клиента')),
                'allow_write_only_authorized_users' => (new Type\Integer())
                    ->setDescription(t('Требовать авторизацию перед отправкой сообщений в поддержку'))
                    ->setCheckboxView(1, 0),
        ]);
    }

    /**
     * Обработчик сохранения объекта
     *
     * @param string $flag
     * @return false|void
     */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['secret_key'] = $this->generateAlias();
            $this['uniq'] = $this->generateAlias();
        }
    }

    /**
     * Обработчик после сохранения объекта
     *
     * @param string $flag
     */
    public function afterWrite($flag)
    {
        $this->updateDefaultFlag($this);
    }

    /**
     * Возвращает случайный сгенерированный уникальный хэш, который будет являться ключем
     *
     * @return string
     */
    public function generateAlias()
    {
        return sha1(uniqid(time(), true));
    }

    /**
     * Проверяет, чтобы оставался хотя бы один профиль по умолчанию
     *
     * @param Profile $save_profile
     */
    public function updateDefaultFlag($save_profile = null)
    {
        $default_count = Request::make()
            ->from($this)
            ->where([
                'site_id' => $this['site_id'],
                'is_default' => 1
            ])->count();

        if ($default_count != 1) {
            $q = Request::make()
                ->update($this)
                ->set([
                    'is_default' => 0
                ])
                ->where([
                    'site_id' => $this['site_id']
                ]);

            if ($save_profile && $save_profile['is_default']) {
                $q->where("id != '#id'", ['id' => $save_profile['id']]);
            }
            $q->exec();

            if (!$save_profile || $default_count == 0) {
                //Устанавливаем первый профиль, профилем по умолчанию
                Request::make()
                    ->update($this)
                    ->set([
                        'is_default' => 1
                    ])
                    ->where([
                        'site_id' => $this['site_id']
                    ])
                    ->limit(1)
                    ->exec();
            }
        }
    }

    /**
     * Обработчик удаления объекта
     *
     * @return bool|void
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            if ($this['is_webhook_enabled']) {
                $this->unsetWebhooks();
            }
            $this->updateDefaultFlag();
        }
        return $result;
    }


    /**
     * Возвращает объект инициализированного телеграм бота
     *
     * @return TgBot
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    function getTelegramBot()
    {
        return TgBot::makeFromProfile($this);
    }

    /**
     * Устанавливает Веб-хуки
     *
     * @return bool
     */
    function setWebhooks()
    {
        try {
            $options = [];
            if ($this['webhook_ssl_certificate']) {
                $options['certificate'] = $this['__webhook_ssl_certificate']->getFullPath();
            }

            $bot = $this->getTelegramBot();
            $bot->setWebhook($this->getWebHookUrl(), $options);

            $this['is_webhook_enabled'] = 1;
            $this->update();

            return true;

        } catch (\Exception $e) {
            return $this->addError($e->getMessage());
        }
    }

    /**
     * Удаляет Веб-хуки
     *
     * @return bool
     */
    function unsetWebhooks()
    {
        try {
            $bot = $this->getTelegramBot();
            $bot->deleteWebhook();

            $this['is_webhook_enabled'] = 0;
            $this->update();

            return true;
        } catch (\Exception $e) {
            return $this->addError($e->getMessage());
        }
    }

    /**
     * Возвращает абсолютный путь
     *
     * @return string
     */
    function getWebHookUrl()
    {
        return Manager::obj()->getUrl('telegram-front-webhook', [
            'secret_key' => $this['secret_key']
        ], true);
    }
}