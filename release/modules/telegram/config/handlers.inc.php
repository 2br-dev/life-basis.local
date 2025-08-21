<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Config;

use RS\Controller\Admin\Helper\CrudCollection;
use RS\Event\HandlerAbstract;
use RS\Html\Toolbar\Button\Button;
use RS\Html\Toolbar\Button\Dropdown;
use RS\Router\Manager;
use RS\Router\Route;
use Telegram\Model\Log\TelegramLog;
use Telegram\Model\Mode\DefaultMode;
use Telegram\Model\Mode\LoginMode;
use Telegram\Model\Mode\LogoutMode;
use Telegram\Model\Platform\PlatformTelegram;

class Handlers extends HandlerAbstract
{
    /**
     * Здесь должна происходить подписка на события
     */
    public function init()
    {
        $this->bind('getroute');
        $this->bind('getlogs');
        $this->bind('telegram.getmodes');
        $this->bind('support.getPlatforms');
        $this->bind('controller.exec.support-admin-topicsctrl.index');
    }

    /**
     * Привносит в систему маршруты модуля Telegram
     *
     * @param array $list
     * @return array
     */
    public static function getRoute($list)
    {
        $list[] = new Route('telegram-front-webhook', '/telegram-webhook/{secret_key}/', [],
            t('Шлюз приема WebHook ов от Telegram'));

        return $list;
    }

    /**
     * Привносит в систему лог взаимодействия с телеграмом
     *
     * @param $list
     * @return array
     */
    public static function getLogs($list)
    {
        $list[] = TelegramLog::getInstance();
        return $list;
    }

    /**
     * Привносит режимы работы в обработку телеграм сообщений
     *
     * @param $list
     * @return array
     */
    public static function telegramGetModes($list)
    {
        $list[] = new DefaultMode();
        $list[] = new LoginMode();
        $list[] = new LogoutMode();

        return $list;
    }

    /**
     * Привносит новую платформу поддержки в систему
     *
     * @param $list
     * @return array
     */
    public static function supportGetPlatforms($list)
    {
        $list[] = new PlatformTelegram();
        return $list;
    }

    /**
     * Добавляет кнопку Telegram в раздел поддержки
     *
     * @param CrudCollection $helper
     */
    public static function controllerExecSupportAdminTopicsCtrlIndex(CrudCollection $helper)
    {
        $router = Manager::obj();
        $helper->getTopToolbar()->addItem(new Dropdown([
            [
                'title' => t('Telegram'),
                'attr' => [
                    'class' => 'button',
                    'onclick' => "JavaScript:\$(this).parent().rsDropdownButton('toggle')"
                ]
            ],
            [
                'title' => t('Пользователи Telegram'),
                'attr' => [
                    'href' => $router->getAdminUrl(false, [], 'telegram-userctrl'),
                ]
            ],
            [
                'title' => t('Перейти к настройкам модуля'),
                'attr' => [
                    'class' => 'dropdown-top-separator',
                    'href' => $router->getAdminUrl('edit', ['mod' => 'telegram'], 'modcontrol-control'),
                ]
            ]
        ]), 'sber', -1);
    }
}