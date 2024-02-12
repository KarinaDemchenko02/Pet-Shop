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
		$products = ProductService::getAllProductsForAdmin();
		$columnsProducts = ProductRepositoryImpl::getColumn();

		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'columnsProducts' => $columnsProducts
		]);

		$template->display();
	}
}
