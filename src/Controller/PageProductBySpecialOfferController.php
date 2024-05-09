<?php

namespace Up\Controller;

use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\SpecialOffer\SpecialOfferRepositoryImpl;
use Up\Service\ProductService\ProductService;
use Up\Util\TemplateEngine\PageSpecialOfferIdTemplateEngine;

class PageProductBySpecialOfferController extends Controller
{
	public function __construct()
	{
		$this->engine = new PageSpecialOfferIdTemplateEngine();
	}

	public function showProductBySpecialOfferAction(Request $request): Response
	{
		$id = $request->getVariable('id');
		$page = $request->getVariable('page');

		if (!(is_numeric($page) || $page > 0))
		{
			$page = 1;
		}

		$specialOfferTitle = SpecialOfferRepositoryImpl::getById($id)->title;

		$products = ProductService::getProductsBySpecialOffer($id, $page);

		$template = $this->engine->getPageTemplate([
														'specialOfferTitle' => $specialOfferTitle,
														'products' => $products,
														'nextPage' => ProductService::getProductsBySpecialOffer(
															$id,
															$page + 1
														),
														'isLogIn' => $this->isLogIn(),
													]);

		// $template->display();
		return new Response(Status::OK, ['template' => $template]);
	}
}
