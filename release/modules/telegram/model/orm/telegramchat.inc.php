<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Orm;

use Longman\TelegramBot\Commands\Command;
use RS\Orm\AbstractObject;
use RS\Orm\Type;
use Telegram\Model\Commands\AbstractSystemCommand;
use Telegram\Model\Mode\AbstractMode;

/**
 * Характеризует состояние пользователя в конкретном чате
 * --/--
 * @property integer $telegram_user_id ID пользователя телеграм
 * @property integer $profile_id Профиль работы с Телеграм ботом
 * @property integer $chat_id Идентификатор чата Telegram
 * @property string $mode Режим работы
 * @property string $submode Подрежим, может использоваться обработчиком режима
 * @property string $state_data Произвольные данные режима в формате JSON
 * --\--
 */
class TelegramChat extends AbstractObject
{
    protected static $table = 'telegram_chat';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'telegram_user_id' => (new Type\Bigint())
                ->setDescription(t('ID пользователя телеграм')),
            'profile_id' => (new Type\Integer())
                ->setDescription(t('Профиль работы с Телеграм ботом')),
            'chat_id' => (new Type\Bigint())
                ->setDescription(t('Идентификатор чата Telegram')),
            'mode' => (new Type\Varchar())
                //В режиме default - сообщения поступают в поддержку (если такой флаг включен)
                //Любой сторонний модуль может привнести свой режим. При переходе в соответствующий режим,
                //все сообщения будут отдаваться на обработку стороннего модуля.
                ->setDescription(t('Режим работы'))
                ->setDefault('default'),
            'submode' => (new Type\Varchar())
                ->setDescription(t('Подрежим, может использоваться обработчиком режима')),
            'state_data' => (new Type\Text())
                ->setDescription(t('Произвольные данные режима в формате JSON'))
        ]);

        $this->addIndex(['telegram_user_id', 'profile_id', 'chat_id'], self::INDEX_PRIMARY);
    }

    /**
     * Возвращает список полей, составляющих первичный ключ
     *
     * @return array
     */
    function getPrimaryKeyProperty()
    {
        return ['telegram_user_id', 'profile_id', 'chat_id'];
    }

    /**
     * Возвращает Произвольные данные, характеризующие текущее состояние
     *
     * @param string $mode
     * @return array|mixed
     */
    function getStateData($mode = null)
    {
        $data = json_decode((string)$this['state_data'], true) ?: [];
        if ($mode) {
            return isset($data[$mode]) ? $data[$mode] : [];
        } else {
            return $data;
        }
    }

    /**
     * Устанавливает Произвольные данные, характеризующее текущее состояние
     *
     * @param $mode
     * @param $data
     */
    function addStateData($mode, $data)
    {
        $state = $this->getStateData();
        $state[$mode] = $data;
        $this['state_data'] = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Возвращает объект, который должен обработать
     * текущий запрос и вернуть ответ в Telegram
     */
    function getModeObject()
    {
        $mode = AbstractMode::getById($this['mode'], $this);
        $mode->setTelegramChat($this);
        return $mode;
    }

    /**
     * Переключает режим работы на новый
     *
     * @param AbstractSystemCommand $command
     * @param AbstractMode $new_mode
     * @return bool
     */
    function switchMode(AbstractSystemCommand $command, AbstractMode $new_mode)
    {
        //if ($this['mode'] != $new_mode->getId())
        {
            if ($this['mode'] != '') {
                $previous_mode = AbstractMode::getById($this['mode'], $this, false);
                if ($previous_mode) {
                    $previous_mode->onLeaveMode($command);
                    //$command->setPreviousMode($previous_mode->getId());
                    $this['submode'] = null;
                }
            }

            $this['mode'] = $new_mode->getId();
            $this->update();

            $new_mode->onEnterMode($command);

            return true;
        }

        return false;
    }

    /**
     * Возвращает профиль работы с Telegram ботом
     *
     * @return Profile
     */
    function getProfile()
    {
        return new Profile($this['profile_id']);
    }

    /**
     * Возвращает объект пользователя Телеграм
     *
     * @return TelegramUser
     */
    function getTelegramUser()
    {
        return TelegramUser::loadByWhere([
            'external_id' => $this['telegram_user_id']
        ]);
    }

    /**
     * Сбрасывает состояние чата
     *
     * @param $telegram_user_id
     * @param $profile_id
     * @param $chat_id
     * @return TelegramChat
     */
    public static function reset($telegram_user_id, $profile_id, $chat_id)
    {
        $telegram_chat = new self();
        $telegram_chat['telegram_user_id'] = $telegram_user_id;
        $telegram_chat['profile_id'] = $profile_id;
        $telegram_chat['chat_id'] = $chat_id;
        $telegram_chat->replace();

        return $telegram_chat;
    }
}