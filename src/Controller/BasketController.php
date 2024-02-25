<?php

namespace Up\Controller;

use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Util\Session;

class BasketController extends Controller
{
	public function addProductAction(Request $request): Response
	{
		$id = $request->getVariable('id');
		$shoppingSession = Session::get('shoppingSession');
		$shoppingSession->addProduct(ProductRepositoryImpl::getById($id), 1);
		$user = Session::get('user');
		if (!is_null($user))
		{
			ShoppingSessionRepositoryImpl::change($shoppingSession);
		}
		return new Response(Status::OK, ['redirect' => '/']);
	}

	public function deleteProductAction(Request $request): Response
	{
		$id = $request->getVariable('id');
		$shoppingSession = Session::get('shoppingSession');
		$shoppingSession->deleteProduct(ProductRepositoryImpl::getById($id));
		$user = Session::get('user');
		if (!is_null($user))
		{
			ShoppingSessionRepositoryImpl::change($shoppingSession);
		}
		return new Response(Status::OK, ['redirect' => '/']);
	}
}
