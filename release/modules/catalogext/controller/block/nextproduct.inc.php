<?php

namespace CatalogExt\Controller\Block;

use \RS\Orm\Type as Type;
use \RS\Controller\Block;

class NextProduct extends Block
{

	public function actionIndex(){

		$currentId = $this->getParam("currentId");
		$product = new \Catalog\Model\Orm\Product();

		$firstId = \RS\Orm\Request::make()
		->select('min(id) as id')
		->from($product)
		->object()->id;


		$nextId = \RS\Orm\Request::make()
		->select("id")
		->from($product)
		->where("id > $currentId")
		->limit(1)
		->orderby('id asc')
		->object()->id;

		if($nextId == null){
			$nextId = $firstId;
		}

		$nextProduct = \RS\Orm\Request::make()
		->from($product)
		->where(['id' => $nextId])
		->setReturnClass('\Catalog\Model\Orm\Product')
		->object();

		$this->view->assign('nextProduct', $nextProduct);
		return $this->result->setTemplate('next-product.tpl');
	}
}