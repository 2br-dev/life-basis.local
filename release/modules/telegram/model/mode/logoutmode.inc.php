<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Model\Mode;

use Longman\TelegramBot\Request;
use Telegram\Model\Commands\AbstractSystemCommand;

/**
 * Обеспечивает работу режима выхода пользователя
 */
class LogoutMode extends AbstractMode
{

    /**
     * Обработчик, вызываемый при входе в данный режим
     *
     * @param AbstractSystemCommand $command Объект обработчика команды от Телеграм
     * @return void
     */
    public function onEnterMode($command)
    {
        $chat = $this->getTelegramChat();

        if ($command->tg_user['user_id'] > 0) {
            $command->tg_user['user_id'] = 0;
            $command->tg_user->update();

            $command->replyToChat(t('Теперь вы не авторизованы'));
        } else {
            $command->replyToChat(t('Вы не авторизованы'));
        }

        $chat->switchMode($command, new DefaultMode());
    }

    /**
     * Возвращает ID режима работы
     *
     * @return string
     */
    public static function getId()
    {
        return 'logout';
    }

    /**
     * Возвращает название режима работы
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('Выход');
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
        return [t('🙁 Выход')];
    }
}