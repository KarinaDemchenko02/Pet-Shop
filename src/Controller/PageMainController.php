<?php

namespace Up\Controller;

use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Util\TemplateEngine\PageMainTemplateEngine;

class PageMainController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageMainTemplateEngine();
	}

	public function showProductsAction()
	{
		$tags = TagService::getAllTags();
		$products = ProductService::getAllProducts();
		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'tags' => $tags,
			]);

		$template->display();
	}
}
