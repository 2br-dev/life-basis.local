<?php

namespace CatalogExt\Controller\Block;

use \RS\Controller\Block;

class ProductList extends Block
{

	public function actionIndex()
	{
		// Получаем рубрику
		$category = new \Catalog\Model\Orm\Dir();
		$product = new \Catalog\Model\Orm\Product();

		$root = \RS\Orm\Request::make()
		->from($category)
		->setReturnClass('\Catalog\Model\Orm\Dir')
		->object();

		$list = \RS\Orm\Request::make()
		->from($product)
		->objects();

		$this->view->assign('list', $list);
		$this->view->assign('category', $root->name);

		return $this->result->setTemplate('productlist.tpl');
	}
}