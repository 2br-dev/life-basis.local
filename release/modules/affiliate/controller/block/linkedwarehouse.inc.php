<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Affiliate\Controller\Block;

use Affiliate\Model\Orm\Affiliate;
use Catalog\Model\WareHouseApi;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\StandartBlock;
use RS\Orm\ControllerParamObject;
use RS\Orm\Type;

/**
 * Блок - связанные склады(магазины)
 */
class LinkedWarehouse extends StandartBlock
{
    protected static $controller_title = 'Связанные склады(магазины)';       //Краткое название контроллера
    protected static $controller_description = 'Отображает связанные с филиалом склады на странице контактов филиала';  //Описание контроллера

    protected $default_params = [
        'indexTemplate' => 'blocks/linkedwarehouse/linked_warehouse.tpl',
        'filter_checkout_public' => 0
    ];

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     *
     * @return ControllerParamObject
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'filter_checkout_public' => new Type\Integer([
                'description' => t('Отображать склады, отмеченные как пункт самовывоза')
            ])
        ]);
    }

    /**
     * @return ResultStandard
     */
    function actionIndex()
    {
        $affiliate = $this->router->getCurrentRoute()->getExtra('affiliate');

        if ($affiliate instanceof Affiliate) {
            $warehouse_api = new WareHouseApi();
            $warehouse_api->setFilter([
                'public' => 1,
                'affiliate_id' => $affiliate['id']
            ]);

            if ($this->getParam('filter_checkout_public')) {
                $warehouse_api->setFilter([
                    'checkout_public' => 1
                ]);
            }

            $warehouses = $warehouse_api->getList();
        } else {
            $warehouses = [];
        }

        $this->view->assign([
            'warehouses' => $warehouses,
            'affiliate' => $affiliate
        ]);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
