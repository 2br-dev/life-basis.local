<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model;

use RS\Http\Request;

class PlatformDetector
{
    const OS_ANDROID = 'android';
    const OS_IOS = 'ios';

    /**
     * Возвращает либо строку с названием платформы, от которой происходит текущий запрос, либо false
     *
     * @return string|bool
     */
    public static function getPlatform()
    {
        $user_agent = Request::commonInstance()->server('HTTP_USER_AGENT');
        $iPod    = stripos($user_agent,"iPod");
        $iPhone  = stripos($user_agent,"iPhone");
        $iPad    = stripos($user_agent,"iPad");
        $Android = stripos($user_agent,"Android");

        if( $iPod || $iPhone || $iPad ){
            return self::OS_IOS;
        } else if($Android) {
            return self::OS_ANDROID;
        }

        return false;
    }
}