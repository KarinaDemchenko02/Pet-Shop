<?php

namespace Up\Controller;

use Up\Repository\Product\ProductRepositoryImpl;

class BasketController extends BaseController
{
	public function addProductAction(int $id)
	{
		$_SESSION['shoppingSession']->addProduct(ProductRepositoryImpl::getById($id), 1);
		header('Location: '."/");
	}

	public function deleteProductAction(int $id)
	{
		$_SESSION['shoppingSession']->deleteProduct(ProductRepositoryImpl::getById($id));
		header('Location: '."/");
	}
}
