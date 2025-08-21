<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\FavoriteApi;
use RS\Controller\StandartBlock;

/**
 * Блок-контроллер збранные товары
 */
class Favorite extends StandartBlock
{
    protected static $controller_title = 'Избранное';
    protected static $controller_description = 'Отображает избранные товары';

    protected $default_params = [
        'indexTemplate' => 'blocks/favorite/favorite.tpl',
    ];

    public $favorite_api;

    function init()
    {
        $this->favorite_api = FavoriteApi::getInstance();
    }

    function actionIndex()
    {
        $product_ids = array_map('intval', array_values($this->favorite_api->loadInFavoriteList()));
        $this->app->addJsVar('favoriteProducts', $product_ids);

        $countFavorites = $this->favorite_api->getFavoriteCount();
        $this->view->assign('countFavorite', $countFavorites);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
