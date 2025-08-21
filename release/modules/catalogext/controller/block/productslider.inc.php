<?php

namespace CatalogExt\Controller\Block;

use ExtCsv\Model\CsvPreset\Catalog;
use \RS\Controller\Block;

class ProductSlider extends Block{

	public function actionIndex(){

		$product = new \Catalog\Model\Orm\Product();

		$products = \RS\Orm\Request::make()
		->from($product)
		->setReturnClass('\Catalog\Model\Orm\Product')
		->objects();

		$this->view->assign('products', $products);
		
		return $this->result->setTemplate('product-slider.tpl');
	}
}