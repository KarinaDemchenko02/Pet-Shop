<?php

namespace Up\Controller;

use Up\Repository\Product\ProductRepositoryImpl;
use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class PageAdminController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
	}

	public function showProductsAction()
	{
		$page = 1;
		if (isset($_GET['page']))
		{
			$page = (int)$_GET['page'];
		}

		$products = ProductService::getAllProductsForAdmin($page);
		$columnsProducts = ProductRepositoryImpl::getColumn();

		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'columnsProducts' => $columnsProducts
		]);

		$template->display();
	}
}
