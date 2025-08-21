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
use RS\Http\Request as HttpRequest;
use RS\Orm\ControllerParamObject;
use RS\Orm\Type;

/**
 * Блок-контроллер "Выбор филиала"
 */
class SelectAffiliate extends StandartBlock
{
    protected static $controller_title = 'Выбор филиала';
    protected static $controller_description = 'Отображает текущий филиал, а также позволяет выбрать другой';

    protected $default_params = [
        'indexTemplate' => 'blocks/selectaffiliate/select_affiliate.tpl', //Должен быть задан у наследника
    ];

    /** @var AffiliateApi */
    public $api;

    function init()
    {
        $this->api = new AffiliateApi();
        $this->api->setFilter('public', 1);
    }

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     *
     * @return ControllerParamObject | false
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'referrer' => (new Type\Varchar())
                ->setVisible(false)
                ->setDescription(t('Адрес текущей страницы')),
        ]);
    }

    /**
     * Отображение филиалов
     */
    function actionIndex()
    {
        $default_referer = $this->url->get('referer', TYPE_STRING, HttpRequest::commonInstance()->selfUri());
        $referrer = $this->getParam('referrer', $default_referer);

        $this->view->assign([
            'current_affiliate' => $this->api->getCurrentAffiliate(),
            'need_recheck' => (int)$this->api->needRecheck(),
            'referrer' => $referrer,
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
