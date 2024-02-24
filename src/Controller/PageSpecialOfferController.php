<?php

namespace Up\Controller;

use Up\Repository\SpecialOffer\SpecialOfferRepositoryImpl;
use Up\Service\ProductService\ProductService;
use Up\Util\TemplateEngine\PageSpecialOfferTemplateEngine;

class PageSpecialOfferController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageSpecialOfferTemplateEngine();
	}

	public function showSpecialOfferAction(): void
	{
		$specialOffers = SpecialOfferRepositoryImpl::getAll();

		$template = $this->engine->getPageTemplate([
													   'specialOffers' => $specialOffers,
													   'isLogIn' => $this->isLogIn(),
												   ]);

		$template->display();
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
													   'nextPage' => ProductService::getProductsBySpecialOffer($id, $page+1),
													   'isLogIn' => $this->isLogIn(),
												   ]);

		$template->display();
	}
}
