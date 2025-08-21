<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Block;
use Catalog\Controller\Front\Product as ControllerProduct;
use \RS\Orm\Type;

/**
* Блок-контроллер Список сопутствующих товаров
*/
class Concomitant extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title       = 'Сопутствующие товары',
        $controller_description = 'Отображает товары, отмеченные как сопутствующие';

    protected
        $default_params = [
            'indexTemplate' => 'blocks/concomitant/concomitant.tpl',
    ];
        

    function actionIndex()
    {
        $shop_config = \RS\Config\Loader::byModule('shop');
        if (!$shop_config){ //Если модуля магазин нет, то и нечего покупать
            return false;
        }
        $route = \RS\Router\Manager::obj()->getCurrentRoute();
        if ($route->getId() == 'catalog-front-product' || $route->getId() == 'shop-front-multioffers') {
            if ($product = $route->getExtra(ControllerProduct::ROUTE_EXTRA_PRODUCT)) {
                if ($shop_config['check_quantity'] && ($product['num'] <= 0)){
                    return false;
                }
                $this->view->assign([
                    'shop_config' => $shop_config,
                    'current_product' => $product,
                    'list' => $product->getConcomitant(),
                ]);
            }
        }
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }

}