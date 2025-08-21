<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Orm;

use RS\Exception;
use RS\Orm\AbstractObject;
use RS\Orm\Type;
use Telegram\Model\Log\TelegramLog;
use Users\Model\Orm\User;

/**
 * Объект пользователя Телеграм.
 * Содержит регистрационные данные пользователя, а также
 * --/--
 * @property integer $external_id ID в Telegram
 * @property integer $user_id Пользователь в ReadyScript
 * @property integer $is_bot Это бот?
 * @property string $first_name Имя пользователя
 * @property string $username Ник-нейм
 * @property string $lang Язык
 * @property string $status Статус пользователя
 * @property integer $ban_type Заблокировать пользователя
 * @property string $ban_expire Дата окончания блокировки
 * @property string $ban_reason Причина блокировки
 * --\--
 */
class TelegramUser extends AbstractObject
{
    const BAN_NONE = 0;
    const BAN_FOREVER = 1;
    const BAN_TEMPORARY = 2;

    const STATUS_MEMBER = 'member';
    const STATUS_KICKED = 'kicked';

    protected static $table = 'telegram_user';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'external_id' => (new Type\Bigint())
                ->setPrimaryKey(true)
                ->setDescription(t('ID в Telegram')),
            'user_id' => (new Type\User())
                //0 - неавторизован, > 0 - авторизован
                ->setDescription(t('Пользователь в ReadyScript'))
                ->setIndex(true)
                ->setAllowEmpty(false),
            'is_bot' => (new Type\Integer())
                ->setDescription(t('Это бот?'))
                ->setCheckboxView(1, 0),
            'first_name' => (new Type\Varchar())
                ->setDescription(t('Имя пользователя')),
            'username' => (new Type\Varchar())
                ->setDescription(t('Ник-нейм')),
            'lang' => (new Type\Varchar())
                ->setDescription(t('Язык')),
            'status' => (new Type\Varchar())
                ->setMaxLength(50)
                ->setDescription(t('Статус пользователя')),
            'ban_type' => (new Type\Integer())
                ->setDescription(t('Заблокировать пользователя'))
                ->setAllowEmpty(false)
                ->setTemplate('%telegram%/admin/profile/ban_type.tpl')
                ->setList([__CLASS__, 'getBanTitles']),
            'ban_expire' => (new Type\Datetime())
                ->setDescription(t('Дата окончания блокировки')),
            'ban_reason' => (new Type\Varchar())
                ->setDescription(t('Причина блокировки'))
                ->setHint(t('Не будет видна пользователю'))
        ]);
    }

    /**
     * Возвращает список возможных типов бана
     *
     * @return array
     */
    public static function getBanTitles()
    {
        return [
            self::BAN_NONE => t('Нет'),
            self::BAN_FOREVER => t('Бессрочно'),
            self::BAN_TEMPORARY => t('На время'),
        ];
    }

    /**
     * Возвращает true, если пользователь забанен в настоящее время
     *
     * @return bool
     */
    public function isBanned()
    {
        return $this['ban_type'] == self::BAN_FOREVER
            || (($this['ban_type'] == self::BAN_TEMPORARY) && strtotime($this['ban_expire']) > time());
    }

    /**
     * Возвращает объект пользователя, исходя из данных сообщения.
     * Возвращает существующего или создает нового пользователя
     *
     * @param \Longman\TelegramBot\Entities\User $from
     * @return self
     * @throws Exception
     */
    public static function getByTgUser(\Longman\TelegramBot\Entities\User $from)
    {
        return self::getByTgUserData($from->getRawData());
    }

    /**
     * Возвращает объект пользователя, исходя из данных о пользователе из Telegram.
     * Возвращает существующего или создает нового пользователя
     *
     * @param array $data
     * @return self
     * @throws Exception
     */
    public static function getByTgUserData($data)
    {
        $self = static::loadByWhere([
            'external_id' => $data['id']
        ]);

        if (!$self['external_id']) {
            $self['external_id'] = $data['id'];
            $self['is_bot'] = $data['is_bot'] ?? 0;
            $self['first_name'] = $data['first_name'];
            $self['username'] = $data['username'];
            $self['lang'] = $data['language_code'];

            if (!$self->insert()) {
                $error = t('Не удалось создать телеграм-пользователя %external_id, %$first_name (%username). Причина: %reason', [
                        'reason' => $self->getErrorsStr()
                    ] + $self->getValues());

                TelegramLog::getInstance()->write($error, TelegramLog::LEVEL_INFO);
                throw new Exception($error);
            } else {
                TelegramLog::getInstance()
                    ->write(t('Создан пользователь %external_id, %$first_name (%username)', $self->getValues()), TelegramLog::LEVEL_INFO);
            }
        }

        return $self;
    }

    /**
     * Возвращает сведения по чату для текущего пользователя
     *
     * @param $profile_id
     * @param $chat_id
     * @return TelegramChat
     */
    public function getTelegramChat($profile_id, $chat_id)
    {
        $chat = TelegramChat::loadByWhere([
            'profile_id' => $profile_id,
            'chat_id' => $chat_id,
            'telegram_user_id' => $this['external_id']
        ]);

        if (!$chat) {
            $chat = TelegramChat::reset(
                $this['external_id'],
                $profile_id,
                $chat_id
            );
        }

        return $chat;
    }

    /**
     * Возвращает связанного пользователя в ReadyScript
     *
     * @return User
     */
    public function getRsUser()
    {
        return new User($this['user_id']);
    }
}