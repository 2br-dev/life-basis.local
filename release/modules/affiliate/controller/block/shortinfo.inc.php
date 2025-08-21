<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Affiliate\Controller\Block;

use Affiliate\Model\AffiliateApi;
use RS\Controller\StandartBlock;

/**
 * Блок контроллер, выводящий информацию о филиале
 */
class ShortInfo extends StandartBlock
{
    protected static $controller_title = 'Краткие контакты филиала';
    protected static $controller_description = 'Отображает краткую контактную информацию о текущем филиале';

    public $api;

    protected $default_params = [
        'indexTemplate' => 'blocks/shortinfo/short_info.tpl',
    ];

    function init()
    {
        $this->api = new AffiliateApi();
    }

    function actionIndex()
    {
        $this->view->assign([
            'current_affiliate' => $this->api->getCurrentAffiliate()
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
