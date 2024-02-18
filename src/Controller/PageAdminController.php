<?php

namespace Up\Controller;

use Up\Dto\ProductChangeDto;
use Up\Exceptions\Service\AdminService\ProductNotChanged;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Util\Json;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class PageAdminController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
	}

	public function indexAction()
	{
		if ($this->isLogInAdmin())
		{
			$this->showProductsAction();
		}
		else
		{
			$this->logInAction();
		}
	}
	private function logInAction()
	{
		$this->engine->getAuthPageTemplate()->display();
	}

	public function showProductsAction()
	{
		$page = 1;
		if (isset($_GET['page']))
		{
			$page = (int)$_GET['page'];
		}
		$products = ProductService::getAllProductsForAdmin($page);
		$columnsProducts = ProductService::getColumn();

//		$orders = OrderService::getAllOrder();
//		$columnsOrders = OrderService::gelColumn();

		$template = $this->engine->getPageTemplate([
			'products' => $products,
//			'orders' => $orders,
			'columnsProducts' => $columnsProducts,
//			'columnsOrders' => $columnsOrders
		]);

		$template->display();
	}

	public function changeAction(): void
	{
		/*if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}*/
		$data = Json::decode(file_get_contents("php://input"));

		$productChangeDto = new ProductChangeDto(
			$data['id'],
			$data['title'],
			$data['description'],
			$data['price'],
			'/images/imgNotFound.png',
		);

		$response = [];
		try
		{
			ProductService::changeProduct($productChangeDto);
			$result = true;
		}
		catch (ProductNotChanged)
		{
			$result = false;
		}

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'Product not changed';
			http_response_code(409);
		}
		echo Json::encode($response);
	}

}
