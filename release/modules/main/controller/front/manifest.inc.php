<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Front;

use Main\Model\ManifestApi;

/**
* Фронт контроллер, позволяющий получить информацию из manifest.json.
*/
class Manifest extends \RS\Controller\Front
{
    public function init()
    {
        $this->wrapOutput(false);
    }

    function actionIndex()
    {
        $api = new ManifestApi();
        $api->manifestToOutput();
    }
}