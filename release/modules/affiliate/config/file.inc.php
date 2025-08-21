<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
 * Класс конфигурации модуля
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'use_geo_ip' => new Type\Integer([
                'description' => t('Использовать GeoIP для определения ближайшего филиала?'),
                'checkboxView' => [1, 0]
            ]),
            'coord_max_distance' => new Type\Real([
                'description' => t('Максимально допустимое отклонение широты и долготы филиала от пользователя, в градусах'),
                'hint' => t('Если филиал будет отдален от координат пользователя более, чем на указанную здесь величину, то он не будет автоматически выбран')
            ]),
            'confirm_city_select' => new Type\Integer([
                'description' => t('Запрашивать подтверждение города у пользователя'),
                'checkboxView' => [1, 0],
                'hint' => t('Запрашивается только один раз при первом посещении сайта')
            ]),
            'replace_vars_in_body' => new Type\Integer([
                'description' => t('Заменять переменные текущего филиала на всех страницах'),
                'hint' => t('Все вхождения в HTML-коде страниц {affiliate_title} или #affiliate_title будут заменены на название филиала. Также будут заменены все произвольные переменные, заданные у филиала. Удобно, при использовании геоподдоменной системы. Вы можете в статье или любом шаблоне использовать строку {affiliate_title}, которая будет заменена, на название текущего филиала. С помощью произвольных переменных, вы можете добавить название филиала в другом падеже или любые другие индивидуальные для филиала сведения и также заменять их на страницах.'),
                'checkboxView' => [1, 0]
            ])
        ]);
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCheckGeoDetection', [], 'affiliate-ctrl'),
                    'title' => t('Проверить геолокацию'),
                    'description' => t('Отобразит окно с определенными координатами и городом филиальной сети'),
                    'class' => 'crud-add crud-sm-dialog'
                ]
            ]
            ];
    }
}
