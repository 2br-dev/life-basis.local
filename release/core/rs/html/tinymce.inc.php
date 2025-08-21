<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html;

use RS\Config\Loader;
use RS\Event\Manager;
use RS\Helper\Tools;
use RS\View\Engine;

/**
* HTML элемент. TinyMce
*/
class Tinymce implements ElementInterface
{
    protected 
        $options,
        $data;
    
    protected static $ids = [];
    private $attributes = [];

    function __construct($options, $data)
    {
        $this->options = array_replace_recursive([
            'tiny_options' => $this->getDefaultTinyOptions(),
            'template' => 'system/admin/html_elements/tinymce/textarea.tpl',
            'tinymce_path' => self::getTinyMCEPath(),
            'jquery_tinymce_path' => self::getTinyMCEPluginPath(),
            'cols' => 100,
            'rows' => 20
        ], $options);

        $this->options = $this->filterOptions($this->options);
        
        $this->data  = $data;
        self::$ids[] = $this->options['id'];
        
        $view = new Engine();
        $view->assign('param', $this->options);
        $view->assign('textarea_ids', implode(",", self::$ids));
    }


    /**
     * Возвращает путь к плагу jQuery tinyMCE
     *
     * @return string
     */
    public static function getTinyMCEPath()
    {
        return \Setup::$JS_PATH.'/tiny_mce/tinymce.min.js';
    }

    /**
     * Возвращает путь к плагу jQuery tinyMCE
     *
     * @return string
     */
    public static function getTinyMCEPluginPath()
    {
        return \Setup::$JS_PATH.'/tiny_mce/jquery.tinymce.min.js';
    }

    /**
     * Возвращает массив принудительно загружаемых плагинов
     *
     * @return array
     */
    public static function getTinyMCEExternalPlugins()
    {
        $plugins = [
            "filemanager" => \Setup::$JS_PATH.'/tiny_mce/filemanager/plugin.min.js'
        ];

        return Manager::fire('tinymce.getExternalPlugins', $plugins)->getResult();
    }

    /**
     * Устанавливает атрибуты для тега textarea
     *
     * @param $attributes
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Возвращает массив атрибутов для тега textarea
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Возвращает строку атрибутов, которую необходимо добавить к HTML
     *
     * @return string
     */
    public function getAttributesAsString()
    {
        $result = [];
        foreach($this->getAttributes() as $key => $value) {
            $result[] = $key.'="'.$value.'"';
        }

        return implode(' ', $result);
    }
    
    /**
    * Возвращает HTML код визуального редактора
    * 
    * @return string
    */
    function getView()
    {
        $view = new Engine();
        $view->assign([
            'param' => $this->options,
            'data' => $this->data,
            'random' => substr(md5($this->options['id'].uniqid(microtime(), true)),0,15),
            'tinymce' => $this
        ]);

        return $view->fetch($this->options['template']);
    }
    
    /**
    * Возвращает настройки tinyMce по умолчанию
    * 
    * @return array
    */
    function getDefaultTinyOptions()
    {
        $tiny_options = [
                'script_url'         => $this->getTinyMCEPath(),
                'menubar'            => false,
                'toolbar_items_size' => 'small',
                'language'           => 'ru',
                'plugins'            => ["link image anchor",
                                              "searchreplace wordcount visualblocks code fullscreen media",
                                              "table responsivefilemanager paste lists"],
                'toolbar1'           => 'undo | bold italic underline strikethrough | removeformat | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect',
                'toolbar2'           => 'table | cut copy paste | searchreplace | bullist numlist | link unlink anchor responsivefilemanager image media code | forecolor backcolor | visualblocks fullscreen',
                'valid_elements'     => '*[*]',
                'valid_children'     => '+body[style]',
                'resize'             => 'both',
                'browser_spellcheck' => true,
                'rel_list'           => [
                    [
                        'title' => 'None',
                        'value' => ''
                    ],
                    [
                        'title' => 'Lightbox',
                        'value' => 'lightbox'
                    ],
                    [
                        'title' => 'nofollow',
                        'value' => 'nofollow'
                    ],
                    [
                        'title' => 'noreferrer',
                        'value' => 'noreferrer'
                    ]
                ],

                'image_advtab'       => true ,
                'paste_data_images'  => true,

                'filemanager_title'          => t("Файловый менеджер"),
                'external_plugins'           => $this->getTinyMCEExternalPlugins(),
                'filemanager_access_key'    => sha1(\Setup::$SECRET_KEY.\Setup::$SECRET_SALT),
                'external_filemanager_path' => \Setup::$JS_PATH.'/tiny_mce/filemanager/',
                
                'relative_urls'      => false,
                
                'cleanup_on_startup' => false,
                'trim_span_elements' => false,
                'verify_html'        => false,
                'cleanup'            => false,
                'remove_script_host' => true
        ];
        
        $config = Loader::getSiteConfig();
        $path_to_theme_css = \Setup::$FOLDER.
                             \Setup::$SM_RELATIVE_TEMPLATE_PATH.
                             '/'.$config->getThemeName().
                             \Setup::$RES_CSS_FOLDER.
                             '/layout.css';
                             
        if (file_exists(\Setup::$ROOT.$path_to_theme_css)) {
            $tiny_options['content_css'] = $path_to_theme_css;
        }
        
        return $tiny_options;
    }

    /**
     * Обрабатывает опции, передаваемые в TinyMCE
     *
     * @param array $options
     * @return mixed
     */
    private function filterOptions($options)
    {
        foreach($options['tiny_options'] as $key => $value) {
            if (preg_match('/^toolbar(\d+)$/', $key, $match)) {
                if (!empty($options['extra_toolbar_buttons'][$match[1]])) {
                    $extra_items = implode(' ', $options['extra_toolbar_buttons'][$match[1]]);
                    $options['tiny_options'][$key] .= ' '.$extra_items;
                }
            }
        }
        return $options;
    }
}