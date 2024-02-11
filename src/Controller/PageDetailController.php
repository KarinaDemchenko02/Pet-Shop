<?php

namespace Up\Controller;

use Up\Service\ProductService\ProductService;
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

	}
}
