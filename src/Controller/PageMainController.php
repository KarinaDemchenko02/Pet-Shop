<?php

namespace Up\Controller;

use JetBrains\PhpStorm\NoReturn;
use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Util\Json;
use Up\Util\TemplateEngine\PageMainTemplateEngine;

class PageMainController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageMainTemplateEngine();
	}

	public function showProductsAction(): void
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
			'isLogIn' => $this->isLogIn(),
			]);

		$template->display();
	}

	public function getTagsJsonAction():void
	{
		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 1;
		}

		$products = ProductService::getProductsByTag((int)$_GET['tag'], $page);

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

		echo JSON::encode([
			'products' => $content,
		]);
	}

	public function getSearchJsonAction(): void
	{
		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 1;
		}

		$products = ProductService::getProductByTitle($_GET['title'], $page);

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

		echo JSON::encode([
			'products' => $content,
		]);
	}

	public function getProductsJsonAction(): void
	{
		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
		{
			$page = $_GET['page'];
		}
		else
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

		echo JSON::encode([
			'products' => $content,
			'nextPage' => ProductService::getAllProducts($page + 1),
		]);
	}

}
