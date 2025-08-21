<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Config;

use RS\Orm\Type;

/**
* Конфигурационный файл модуля
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'make_default_description_from_title' => new Type\Integer([
                'description' => t('Заполнять мета-тег description (если он пустой) из title'),
                'checkboxView' => [1, 0]
            ])
        ]);
    }
}

