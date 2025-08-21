<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Front;

use Main\Model\Microdata\MicrodataOrganization;
use RS\Application\Application;
use RS\Config\Loader;

/**
* Front контроллер - заглушка.
*/
class Stub extends \RS\Controller\Front
{
    function actionIndex()
    {
        $config = Loader::byModule($this);
        if ($config['microdata_organization']) {
            //Добавляем микроразметку для главной страницы, если включена опция
            Application::getInstance()->microdata->addMicrodata(new MicrodataOrganization( Loader::getSiteConfig() ));
        }

        return $this->result;
    }
}
