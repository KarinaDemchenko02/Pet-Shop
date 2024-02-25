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

		try
		{
			$product = ProductService::getProductById($id);
		}
		catch (ProductNotFound)
		{
			return new Response(Status::NOT_FOUND, ['errors' => 'Product not found']);
		}
		$template = $this->engine->getPageTemplate(['productDto' => $product, 'isLogIn' => $this->isLogIn()]);
		return new Response(Status::OK, ['template' => $template]);
	}

	public function buyProductAction(int $id)
	{
		$product = ProductRepositoryImpl::getById($id);
		try
		{
			if ($this->isLogIn())
			{
				$userId = Session::get('user')->id;
			}
			else
			{
				$userId = null;
			}

			$orderDto = new OrderAddingDto(
				new ShoppingSession(
					null, $userId, [new ProductQuantity($product, 1)]
				), $_POST['name'], $_POST['surname'], $_POST['address'],
			);
			OrderService::createOrder($orderDto);
			header('Location: /success/');
		}
		catch (OrderNotCompleted)
		{
			echo "fail";
		}
	}

	public function showModalSuccess(): void
	{
		$this->engine->viewModalSuccess()->display();
	}
}
