<?php

namespace Up\Controller;

use Up\Model\Product;
use Up\Service\Template;

class PageMainController extends BaseController
{
	public function showProductsAction()
	{
		$products =  [(new Product(1, 'title', 'desc', 1000, 'status', [1, 2])),
			(new Product(2, 'title2', 'desc2', 2000, 'status2', [1, 2]))];
		// Обращаемся к сервису -> он обращается к репозиторию -> репозиторий возвращает данные -> ... ->

		$productTemplates = [];
		foreach ($products as $product)
		{
			$productTemplates[] = new Template('components/main/product',
				[
					'title' => $product->title,
					'desc' => $product->description,
					'id' => $product->id,
					]
			);
		}
		$mainPageTemplate = new Template('page/main/main', ['products' => $productTemplates], );
		$layoutTemplate = new Template('layout', [
			'content' => $mainPageTemplate,
		]);

		$layoutTemplate->display();
	}
}
