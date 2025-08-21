<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Dirapi;
use Catalog\Model\Logtype\ShowProduct as LogtypeShowProduct;
use Catalog\Model\Microdata\MicrodataProduct;
use Catalog\Model\Orm\Dir;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use RS\Debug\Action as DebugAction;
use RS\Debug\Tool as DebugTool;
use RS\Helper\BotDetector;
use Users\Model\LogApi as UserLogApi;

/**
* Просмотр одного товара
*/
class Product extends Front
{
    const ROUTE_EXTRA_PRODUCT = 'product';

    protected $id;
    protected $lastpage;
    
    /** @var ProductApi */
    public $api;
    /** @var Dirapi */
    public $dirapi;
    public $config;
    public $tab;
    
    function init()
    {
        $this->id     = $this->url->get('id', TYPE_STRING);
        $this->tab    = $this->url->get('tab', TYPE_STRING);
        $this->api    = new ProductApi();
        $this->dirapi = new Dirapi();
        $this->config = ConfigLoader::byModule($this);
    }
    
    /**
    * Обычный просмотр товара
    */
    function actionIndex()
    {
        /**
        * @var \Catalog\Model\Orm\Product $item
        */
        $item = $this->api->getById($this->id);
        if (!$item){
            $this->e404(t('Такого товара не существует'));
        }

        if ($this->tab && !in_array($this->tab, (array)$this->config->tabs_on_new_page)) {
            $this->e404(t('Некорректно указана вкладка'));
        }

        //Если нужно скрывать скрытый товар
        if ((!$item['public'] || ($this->config['hide_unobtainable_goods'] == 'Y' && $item['num']<=0)) && $this->config['not_public_product_404']){
            $this->e404();
        }

        //Если есть alias у товара и открыта страница с id вместо alias, то редирект
        $this->checkRedirectToAliasUrl($this->id, $item, $item->getUrl());

        $item->fillAffiliateDynamicNum();
        $item->fillCategories();
        $item->fillCost();
        $item->fillProperty(true);
        $item->calculateUserCost();

        // Прикрепляем к маршруту загруженый объект товара для использования в блоках
        $this->router->getCurrentRoute()->addExtra(self::ROUTE_EXTRA_PRODUCT, $item);

        // Устаревший способ, будет удален в будущих версиях. Временно оставлен для совместимости со старыми модулями
        @$this->router->getCurrentRoute()->product = $item;
        
        if ($debug_group = $this->getDebugGroup()) {
            $edit_href = $this->router->getAdminUrl('edit', ['id' => $item['id']], 'catalog-ctrl');
            $debug_group->addDebugAction(new DebugAction\Edit($edit_href));
            $debug_group->addTool('create', new DebugTool\Edit($edit_href));
        } 
        
        //Находим путь к товару
        $dir_id = $this->dirapi->getBreadcrumbDir($item);
        if ($dir_id) {
            $dir_obj = $this->dirapi->getById($dir_id);
            if ($dir_obj) {
                $path = $item->getItemPathLine( $dir_obj['id'] );
            }
        }
        
        if (!isset($path)) {
            /** @var Dir[] $path */
            $path = $this->dirapi->getPathToFirst( $item['maindir'] );
        }
        
        foreach($path as $dir) {
            if ($dir['public']) {
                $this->app->breadcrumbs->addBreadCrumb($dir['name'], $dir->getUrl(), null,['id' => $dir['id']] );
            }
        }
        
        $this->view->assign([
            'path' => $path,
            'product' => $item,
            'back_url' => $this->url->getSavedUrl('catalog.list.index'),
            'tab' => $this->tab
        ]);
        
        //Заполняем meta теги
        $item_title       = $item->getMetaTitle();
        $item_keywords    = $item->getMetaKeywords();
        $item_description = $item->getMetaDescription();
        
        foreach($path as $one_dir) {
            $seoGenDir = new \Catalog\Model\SeoReplace\Dir([
                $one_dir,
            ]);
            if ($this->config['concat_dir_meta']) {
                $this->app->title->addSection(!empty($one_dir['meta_title']) ? $seoGenDir->replace($one_dir['meta_title']) : $one_dir['name']);
            }
            if ($this->config['concat_dir_meta'] || !$item_keywords) {
                $this->app->meta->addKeywords( !empty($one_dir['meta_keywords']) ? $seoGenDir->replace($one_dir['meta_keywords']) : $one_dir['name'] );
            }
        }
        
        //Инициализируем SEO генератор
        $seoGen = new \Catalog\Model\SeoReplace\Product([
            $item,
            'cat_' => $item->getMainDir(),
            'brand_' => $item->getBrand(),
            'price' => $item->getCost(),
        ]);
        
        //Подменим значния в переменных
        $item_title       = $seoGen->replace($item_title);
        $item_keywords    = $seoGen->replace($item_keywords);
        $item_description = $seoGen->replace($item_description);
        
        //Если мета теги назначены у самого товара
        if ($item['meta_title']) {
            $item_title = $seoGen->replace($item['meta_title']);
            $this->app->title->clean();
        }
        
        if ($item['meta_keywords']) {
            $item_keywords = $seoGen->replace($item['meta_keywords']);
            $this->app->meta->cleanMeta('keywords');
        }
        
        if ($item['meta_description']) {
            $item_description   = $seoGen->replace($item['meta_description']);
            $this->app->meta->cleanMeta('description');
        }

        if ($this->tab) {
            $tab_list = $this->config->__tabs_on_new_page->getList();
            $this->app->title->addSection($tab_list[$this->tab]);
            $this->app->meta->addDescriptions($tab_list[$this->tab]);
        }

        
        $this->app->title->addSection(!empty($item_title) ? $item_title : $item['title']);
        $this->app->meta->addKeywords($item_keywords);
        $this->app->meta->addDescriptions($item_description);

        $this->app->microdata->addMicrodata(new MicrodataProduct($item));

        if (!BotDetector::isBot()) {
            //Пишем лог для виджета - "Что смотрят на сайте"
            UserLogApi::appendUserLog(new LogtypeShowProduct(), $item['id'], null, $item['id']);
        }
        return $this->result->setTemplate( 'product.tpl' );
    }
}
