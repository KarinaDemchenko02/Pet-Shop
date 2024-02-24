<?php

namespace Up\Controller;

use Up\Service\ProductService\ProductService;
use Up\Util\TemplateEngine\PageSpecialOfferIdTemplateEngine;

class PageProductBySpecialOfferController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageSpecialOfferIdTemplateEngine();
	}

	public function showProductBySpecialOfferAction(int $id): void
	{
		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 1;
		}

		$products = ProductService::getProductsBySpecialOffer($id, $page);

		$template = $this->engine->getPageTemplate([
													   'products' => $products,
													   'nextPage' => ProductService::getProductsBySpecialOffer(
														   $id,
														   $page + 1
													   ),
													   'isLogIn' => $this->isLogIn(),
												   ]);

		$template->display();
	}
}
