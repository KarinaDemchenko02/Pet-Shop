<?php

namespace Up\Controller;

use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\SpecialOffer\SpecialOfferRepositoryImpl;
use Up\Service\ProductService\ProductService;
use Up\Util\TemplateEngine\PageSpecialOfferTemplateEngine;

class PageSpecialOfferController extends Controller
{
	public function __construct()
	{
		$this->engine = new PageSpecialOfferTemplateEngine();
	}

	public function showSpecialOfferAction(Request $request): Response
	{
		$specialOffersPreviewProducts = SpecialOfferRepositoryImpl::getPreviewProducts();

		$template = $this->engine->getPageTemplate([
													   'specialOffersPreviewProducts' => $specialOffersPreviewProducts,
													   'isLogIn' => (bool)$request->getDataByKey('email'),
												   ]);

		// $template->display();
		return new Response(Status::OK, ['template' => $template]);
	}
}
