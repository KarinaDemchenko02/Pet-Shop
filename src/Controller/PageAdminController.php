<?php

namespace Up\Controller;

use Up\Repository\Product\ProductRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Util\Json;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;
use Up\Util\Upload;

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

	public function uploadAction(): void
	{
		Upload::upload();
		$this->indexAction();
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

		$orders = OrderService::getAllOrder();
		$columnsOrders = OrderService::gelColumn();

		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'orders' => $orders,
			'columnsProducts' => $columnsProducts,
			'columnsOrders' => $columnsOrders
		]);

		$template->display();
	}
}
