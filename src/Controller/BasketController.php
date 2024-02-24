<?php

namespace Up\Controller;

use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Util\Session;

class BasketController extends BaseController
{
	public function addProductAction(int $id)
	{
		$shoppingSession = Session::get('shoppingSession');
		$shoppingSession->addProduct(ProductRepositoryImpl::getById($id), 1);
		$user = Session::get('user');
		if (!is_null($user))
		{
			ShoppingSessionRepositoryImpl::change($shoppingSession);
		}
		header('Location: ' . "/");
	}

	public function deleteProductAction(int $id)
	{
		$shoppingSession = Session::get('shoppingSession');
		$shoppingSession->deleteProduct(ProductRepositoryImpl::getById($id));
		$user = Session::get('user');
		if (!is_null($user))
		{
			ShoppingSessionRepositoryImpl::change($shoppingSession);
		}
		header('Location: ' . "/");
	}
}
