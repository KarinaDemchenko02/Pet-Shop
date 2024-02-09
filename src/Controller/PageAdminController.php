<?php

namespace Up\Controller;

use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Service\UserService\UserService;
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
		$users = UserService::getAllUsers();

		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'tags' => $tags,
			'users' => $users
		]);

		$template->display();
	}
}