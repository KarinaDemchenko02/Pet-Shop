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
		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 1;
		}
		if (isset($_GET['title']))
		{
			$products = ProductService::getProductByTitle($_GET['title'], $page);
		}
		elseif (isset($_GET['tag']) && is_numeric($_GET['tag']) && $_GET['tag'] > 0)
		{
			$products = ProductService::getProductsByTag((int)$_GET['tag'], $page);
		}
		else
		{
			$products = ProductService::getAllProducts($page);
		}
		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'tags' => $tags,
			'isLogIn' => $this->isLogIn(),
			]);

		$template->display();
	}
}
