<?php

namespace Up\Controller;

use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Util\TemplateEngine\PageMainTemplateEngine;

class PageMainController extends Controller
{
	public function __construct()
	{
		$this->engine = new PageMainTemplateEngine();
	}

	public function showProductsAction(Request $request): Response
	{
		$page = $request->getVariable('page');
		$titleParam = $request->getVariable('title');
		$tagParam = $request->getVariable('tag');

		$tags = TagService::getAllTags();
		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		if (!is_null($titleParam))
		{
			$products = ProductService::getProductByTitle($titleParam, $page);
		}
		elseif (is_null($tagParam) && is_numeric($tagParam) && $tagParam > 0)
		{
			$products = ProductService::getProductsByTag((int)$tagParam, $page);
		}
		else
		{
			$products = ProductService::getAllProducts($page);
		}

		$template = $this->engine->getPageTemplate([
			'products' => $products,
			'tags' => $tags,
			'nextPage' => ProductService::getAllProducts($page + 1),
			'isLogIn' => (bool)$request->getDataByKey('user'),
			]);

		return new Response(Status::OK, ['template' => $template]);
	}
}
