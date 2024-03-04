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

		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		$tags = TagService::getAllTags();

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

		$content = [];

		foreach ($products as $product)
		{
			$content[] =
				[
					'title' => $product->title,
					'description' => $product->description,
					'price' => $product->price,
					'id' => $product->id,
					'imagePath' => $product->imagePath,
				];
		}

		$template = $this->engine->getPageTemplate([
			'products' => $content,
			'tag' => $tags,
			'nextPage' => ProductService::getAllProducts($page + 1),
			'isLogIn' => (bool)$request->getDataByKey('email'),
			]);

		return new Response(Status::OK, ['template' => $template]);
	}

	public function getTagsJsonAction(Request $request): Response
	{
		$page = $request->getVariable('page');
		$tagParam = $request->getVariable('tag');
		
		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		$products = ProductService::getProductsByTags(array($tagParam), $page);

		$nextPage = ProductService::getProductsByTags(array($tagParam), $page + 1);

		$allProducts = ProductService::getAllProducts($page);

		$content = [];
		foreach ($products as $product)
		{
			$content[] = [
				'title' => $product->title,
				'description' => $product->description,
				'price' => $product->price,
				'id' => $product->id,
				'imagePath' => $product->imagePath,
			];
		}

		return new Response(Status::OK, ['products' => $content, 'nextPage' => $nextPage, 'allProducts' => $allProducts]);
	}

	public function getSearchJsonAction(Request $request): Response
	{
		$page = $request->getVariable('page');
		$titleParam = $request->getVariable('title');
		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		$products = ProductService::getProductByTitle((string)$titleParam, $page);
		$nextPage  = ProductService::getProductByTitle((string)$titleParam, $page+1);

		$content = [];
		foreach ($products as $product)
		{
			$content[] = [
				'title' => $product->title,
				'description' => $product->description,
				'price' => $product->price,
				'id' => $product->id,
				'imagePath' => $product->imagePath,
			];
		}

		return new Response(Status::OK, ['title' => $titleParam, 'products' => $content, 'nextPage' => $nextPage]);
	}

	public function getProductsJsonAction(Request $request): Response
	{
		$page = $request->getVariable('page');

		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		$products = ProductService::getAllProducts($page);
		$content = [];
		foreach ($products as $product)
		{
			$content[] = [
				'title' => $product->title,
				'description' => $product->description,
				'price' => $product->price,
				'id' => $product->id,
				'imagePath' => $product->imagePath,
			];
		}

		return new Response(Status::OK, [
			'products' => $content,
			'nextPage' => ProductService::getAllProducts($page + 1),
			]);
	}

}
