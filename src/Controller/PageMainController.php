<?php

namespace Up\Controller;

use Up\Service\ProductService\ProductService;
use Up\Util\TemplateEngine\PageMainTemplateEngine;

class PageMainController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageMainTemplateEngine();
	}

	public function showProductsAction()
	{

		$products = ProductService::getAllProducts();
		$template = $this->engine->getPageTemplate($products);
		$template->display();
	}
}
