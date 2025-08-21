<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

use RS\Controller\Result\Standard;

class Compare extends \RS\Controller\Front
{
    public
        $api;
        
    function init()
    {
        $this->api = \Catalog\Model\Compare::currentCompare();        
    }

    /**
     * Отображает страницу сравнения
     *
     * @return Standard
     */
    function actionIndex()
    {
        $this->view->assign('comp_data', $this->api->getCompareData());
        return $this->result->setTemplate( 'compare.tpl' );  
    }

    /**
     * Удалет один товар из сравнения
     *
     * @return Standard
     */
    function actionRemove()
    {
        $id = $this->url->post('id', TYPE_INTEGER);
        $result = $this->api->removeProduct($id);
        return $this->result->setSuccess($result)
                            ->addSection('products', array_keys($this->api->getList()))
                            ->addSection('total', $this->api->getCount());
    }                
}