<?php

namespace Up\Controller;

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
		$products = ProductService::getAllProducts();
		$tags = TagService::getAllTags();

		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'tags' => $tags,
			'users' => [],
		]);

		$template->display();
	}
}
