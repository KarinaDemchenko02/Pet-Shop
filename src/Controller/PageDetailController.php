<?php

namespace Up\Controller;

use Up\Dto\OrderAddingDto;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Entity\ProductQuantity;
use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Util\Session;
use Up\Util\TemplateEngine\PageDetailTemplateEngine;

class PageDetailController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageDetailTemplateEngine();
	}

	public function showProductAction(int $id)
	{
		$product = ProductService::getProductById($id);
		$template = $this->engine->getPageTemplate(['productDto' => $product, 'isLogIn' => $this->isLogIn()]);
		$template->display();
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
			OrderService::buyProduct($orderDto);
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
