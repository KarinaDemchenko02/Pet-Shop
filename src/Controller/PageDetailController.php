<?php

namespace Up\Controller;

use Up\Dto\OrderAddingDto;
use Up\Exceptions\Service\OrderService\OrderNotCompleted;
use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Util\Session;
use Up\Util\TemplateEngine\PageDetailTemplateEngine;
use Up\Util\TemplateEngine\Template;

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
				$userId,
				$_POST['name'],
				$_POST['surname'],
				$_POST['address'],
				$id,
			);
			OrderService::buyProduct($orderDto);
			header('Location: /success/');
		}
		catch (OrderNotCompleted)
		{
			echo "fail";
		}
	}
	public function showModalSuccess(): void {
		$this->engine->viewModalSuccess()->display();
	}
}
