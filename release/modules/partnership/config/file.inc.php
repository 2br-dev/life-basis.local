<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Partnership\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
 * Конфигурационный файл модуля
 */
class File extends ConfigObject
{
    const COOKIE_REDIRECTED_TO_GEO_PARTNER = 'redirected_to_geo_partner';
    const COOKIE_GEO_PARTNER_CONFIRMATION_SHOWN = 'geo_partner_confirmation_shown';
    const PARAM_DONT_REDIRECT_TO_GEO_PARTNER = 'partnership_redirect';
    const PARAM_DONT_SHOW_CONFIRMATION_GEO_PARTNER = 'partnership_confirmed';

    function _init()
    {
        parent::_init()->append([
            'main_title' => new Type\Varchar([
                'description' => t('Название для основного сайта'),
                'hint' => t('Используется в качестве "Названия партнёра" при нахождении на основном сайте.'),
            ]),
            'main_short_contacts' => new Type\Varchar([
                'description' => t('Короткая контактная информация основного сайта'),
            ]),
            'main_contacts' => new Type\Richtext([
                'description' => t('Контактная информация основного сайта')
            ]),
            'coordinates' => new Type\Coordinates([
                'description' => t('Местонахождение основного магазина'),
                'runtime' => false,
            ]),
            'redirect_to_geo_partner' => new Type\Integer([
                'description' => t('Перенаправлять на ближайший партнёркий сайт, используя геолокацию'),
                'checkboxView' => [1, 0],
            ]),
            'show_confirmation_geo_partner' => new Type\Integer([
                'description' => t('Показывать окно подтверждения филиала'),
                'checkboxView' => [1, 0],
            ]),
        ]);
    }

    function isMultisiteConfig()
    {
        return false;
    }
}
