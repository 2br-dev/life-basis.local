<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Config;

use Kaptcha\Model\CaptchaType\BotCatcher;
use Kaptcha\Model\CaptchaType\ReCaptcha3;
use Kaptcha\Model\CaptchaType\RSDefault;
use RS\Event\HandlerAbstract;

class Handlers extends HandlerAbstract
{
    function init()
    {
        $this->bind('captcha.gettypes');
    }

    /**
     * Для совместимости со старыми версиями
     */
    public static function getRoute($routes)
    {}

    /**
    * Добавляем стандартную капчу
    */
    public static function captchaGetTypes($list)
    {
        $list[] = new RSDefault();
        $list[] = new ReCaptcha3();
        $list[] = new BotCatcher();
        return $list;
    }
}
