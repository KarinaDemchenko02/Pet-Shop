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
}
