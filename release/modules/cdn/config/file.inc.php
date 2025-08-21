<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace CDN\Config;
use \RS\Orm\Type;

/**
 * @property string domain
 */
class File extends \RS\Orm\ConfigObject
{
    const
        CDN_ELEMENT_CSS = 'css',
        CDN_ELEMENT_JS = 'js',
        CDN_ELEMENT_IMG = 'img';
        
    function _init()
    {
        parent::_init()->append([
            'domain' => new Type\Varchar([
                'description' => t('CDN домен'),
                'hint' => t('Например, yourdomain.cdnvideo.ru')
            ]),
            'cdn_elements' => new Type\ArrayList([
                'description' => t('Объекты для ускорения'),
                'runtime' => false,
                'CheckboxListView' => true,
                'listFromArray' => [[
                    self::CDN_ELEMENT_CSS => 'CSS',
                    self::CDN_ELEMENT_JS => 'JavaScript',
                    self::CDN_ELEMENT_IMG => t('Изображения')
                ]]
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
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('registrationForm', [], 'cdn-ctrl'),
                    'title' => t('Подключиться к CDN'),
                    'description' => t('Отправить заявку на регистрацию услуги "Ускорение Web-контента" в компанию CDNvideo.ru'),
                    'class' => 'crud-add',
                ],
            ]
            ];
    }
    
    function afterWrite($flag)
    {
        parent::afterWrite($flag);
        
        if ($flag == self::UPDATE_FLAG) {
            \RS\Cache\Cleaner::obj()->clean(\RS\Cache\Cleaner::CACHE_TYPE_COMMON);
        }
    }

}