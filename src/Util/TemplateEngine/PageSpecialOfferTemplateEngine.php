<?php

namespace Up\Util\TemplateEngine;

use Up\Util\Session;

class PageSpecialOfferTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$specialOffersPreviewProducts = $variables['specialOffersPreviewProducts'];
		$isLogIn = $variables['isLogIn'];


		$footer = new Template('components/main/footer');
		$header = new Template('components/specialOffers/header');
		$form = new Template('components/main/formAuthorization');
		$basket = $this->getBasketTemplate(Session::get('shoppingSession')->getProducts());

		$specialOfferPageTemplate = new Template('page/specialOffers/specialOffers', [
			'specialOffersPreviewProducts' => $this->getSpecialOffersSectionTemplate($specialOffersPreviewProducts),
			'form' => $form,
			'isLogIn' => $isLogIn,
			'basket' => $basket,
		],);

		return (new Template('layout', [
			'header' => $header,
			'content' => $specialOfferPageTemplate,
			'footer' => $footer,
		]));
	}

	public function getBasketTemplate(array $basketItems): Template
	{
		$basketItemsTemplates = [];
		foreach ($basketItems as $item)
		{
			$basketItemsTemplates[] = new Template('components/main/basketItem', [
				'title' => $item->info->title,
				'price' => $item->info->price,
				'id' => $item->info->id,
				'imagePath' => $item->info->imagePath,
				'quantity' => $item->getQuantity(),
			]);
		}
		return new Template('components/main/basket', ['items' => $basketItemsTemplates]);
	}

	public function getSpecialOffersSectionTemplate(array $specialOffersPreviewsProducts): array
	{
		$specialOffersTemplates = [];
		foreach ($specialOffersPreviewsProducts as $specialOffersPreview)
		{
			$specialOffersTemplates[] = new Template('components/specialOffers/specialOfferSection',
				[
					'description' =>$specialOffersPreview->specialOffer->description,
					'title' => $specialOffersPreview->specialOffer->title,
					'id' => $specialOffersPreview->specialOffer->id,
					'startDate' => $specialOffersPreview->specialOffer->startDate,
					'endDate' => $specialOffersPreview->specialOffer->endDate,
					'products' => $this->getProductsSectionTemplate($specialOffersPreview->getProducts()),
				]
			);
		}
		return $specialOffersTemplates;
	}

	private function getProductsSectionTemplate($products)
	{
		$productTemplates = [];
		foreach ($products as $product)
		{
			$productTemplates[] = new Template('components/main/product',
											   [
												   'title' => $product->title,
												   'desc' => $product->description,
												   'price' => $product->price,
												   'id' => $product->id,
												   'imagePath' => $product->imagePath,
											   ]
			);
		}
		return $productTemplates;
	}
}
