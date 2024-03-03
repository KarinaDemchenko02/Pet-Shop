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

		$specialOfferTitle = SpecialOfferRepositoryImpl::getById($id)->title;

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
