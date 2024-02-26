<?php

namespace Up\Controller;

use Up\Dto\Order\OrderAddingDto;
use Up\Entity\ProductQuantity;
use Up\Entity\ShoppingSession;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Exceptions\Product\ProductNotFound;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Util\Session;
use Up\Util\TemplateEngine\PageDetailTemplateEngine;

class PageDetailController extends Controller
{
	public function __construct()
	{
		$this->engine = new PageDetailTemplateEngine();
	}

	public function showProductAction(Request $request): Response
	{
		$id = $request->getVariable('id');

		$isLogIn = $request->getDataByKey('email') !== null;

		try
		{
			$product = ProductService::getProductById($id);
		}
		catch (ProductNotFound)
		{
			return new Response(Status::NOT_FOUND, ['errors' => 'Product not found']);
		}
		$template = $this->engine->getPageTemplate(['productDto' => $product, 'isLogIn' => $isLogIn]);
		return new Response(Status::OK, ['template' => $template]);
	}

	public function buyProductAction(Request $request): Response
	{
		$id = $request->getVariable('id');
		$data = $request->getDataByKey('jwt')['data'];
		$userId = $data['id'];

		$product = ProductRepositoryImpl::getById($id);
		try
		{
			$orderDto = new OrderAddingDto(
				new ShoppingSession(
					null, $userId, [new ProductQuantity($product, 1)]
				), $request->getDataByKey('name'),  $request->getDataByKey('surname'), $request->getDataByKey('address'),
			);
			OrderService::createOrder($orderDto);
			return new Response(Status::OK, ['redirect' => '/success/']);
		}
		catch (OrderNotCompleted)
		{
			return new Response(Status::BAD_REQUEST);
		}
	}

	public function showModalSuccess(Request $request): Response
	{
		return new Response(Status::OK, ['template' => $this->engine->viewModalSuccess()]);
	}
}
