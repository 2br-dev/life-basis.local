<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Block;
use \RS\Orm\Type;

/**
* Блок - логотип сайта
*/
class Logo extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Логотип сайта',
        $controller_description = 'Отображает логотип с гиперссылкой';
    
    protected
        $default_params = [
            'indexTemplate' => 'blocks/logo/logo.tpl',
            'width' => '200',
            'height' => '100'
    ];
    
    /**
    * Возвращает ORM объект, содержащий настриваемые параметры или false в случае, 
    * если контроллер не поддерживает настраиваемые параметры
    * @return \RS\Orm\ControllerParamObject | false
    */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
                'link' => new Type\Varchar([
                    'description' => t('Ссылка'),
                    'hint' => t('Если ничего не указано, то будет ссылаться на корень сайта. Пробел будет означать - отсутствие ссылки')
                ]),
                'width' => new Type\Integer([
                    'description' => t('Ширина изображения, px'),
                ]),
                'height' => new Type\Integer([
                    'description' => t('Высота изображения, px'),
                ]),
                'logo_type' => new Type\Varchar([
                    'description' => t('Тип используемого логотипа'),
                    'listFromArray' => [[
                        'logo' => t('Обычный лого'),
                        'logo_inverse' => t('Инверсированный лого')
                    ]]
                ])
        ]);
    }
    

    function actionIndex()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        $site_root = \RS\Site\Manager::getSite()->getRootUrl();
        $link = $this->getParam('link') == '' ? $site_root : $this->getParam('link');
        
        $this->view->assign([
            'site_config' => $site_config,
            'link' => $link,
            'width' => $this->getParam('width', $this->default_params['width'], true),
            'height' => $this->getParam('height', $this->default_params['height'], true),
            'logo_field' => $this->getParam('logo_type') ?: 'logo'
        ]);
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}