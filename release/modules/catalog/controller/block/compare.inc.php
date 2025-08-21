<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;

use Catalog\Model\Compare as CompareApi;
use RS\Controller\Result\Standard;
use RS\Controller\StandartBlock;
use RS\Orm\Type;

/**
* Блок-контроллер Список категорий
*/
class Compare extends StandartBlock
{
    protected static
        $controller_title = 'Сравнение товаров',
        $controller_description = 'Отображает товары, которые были добавлены к сравнению. В данном блоке товары можно исключить или перейти к сравнению';
    
    protected
        $action_var = 'cpmdo',
        $default_params = [
            'indexTemplate' => 'blocks/compare/compare.tpl',
            'listTemplate' => 'blocks/compare/items.tpl'
    ];
    
    /** @var CompareApi */
    public $api;
    
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'listTemplate' => new Type\Template([
                'description' => t('Шаблон списка')
            ])
        ]);
    }
       
    function init()
    {
        $this->api = CompareApi::currentCompare();
    }

    /**
     * Возвращает блок сравнения товаров
     *
     * @return Standard
     */
    function actionIndex()
    {
        $product_ids = array_map('intval', array_values($this->api->getList()));
        $this->app->addJsVar('compareProducts', $product_ids);

        $this->view->assign('list_html', $this->actionAjaxGetItems()->getHtml());
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }

    /**
     * Возвращает список товаров в сравнении
     *
     * @return Standard
     */
    function actionAjaxGetItems()
    {
        $list = $this->api->getCompareList();
        $this->view->assign('list', $list);
        return $this->result
            ->addSection('products', array_keys($list))
            ->addSection('total', $this->api->getCount())
            ->setTemplate( $this->getParam('listTemplate') );
    }

    /**
     * Добавляет один товар к сравнению
     *
     * @return Standard
     */
    function actionAjaxAdd()
    {
        $id = $this->url->post('id', TYPE_INTEGER);
        $this->result->setSuccess( $this->api->addProduct($id) );
        return $this->actionAjaxGetItems();
    }

    /**
     * Удаляет один товар из сравнения
     *
     * @return Standard
     */
    function actionAjaxRemove()
    {
        $id = $this->url->post('id', TYPE_INTEGER);
        $result = $this->api->removeProduct($id);

        return $this->result
                        ->setSuccess($result)
                        ->addSection('products', array_keys($this->api->getList()))
                        ->addSection('total', $this->api->getCount());
    }

    /**
     * Удаляет все товары из сравнения
     *
     * @return Standard
     */
    function actionAjaxRemoveAll()
    {
        return $this->result
                ->setSuccess( $this->api->removeAll() )
                ->addSection('products', []);
    }
}
