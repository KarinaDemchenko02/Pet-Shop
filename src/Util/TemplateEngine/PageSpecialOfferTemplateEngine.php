<?php

namespace Up\Util\TemplateEngine;

use Up\Util\Session;

class PageSpecialOfferTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$specialOffers = $variables['specialOffers'];
		$isLogIn = $variables['isLogIn'];


		$footer = new Template('components/main/footer');
		$header = $this->getHeaderTemplate($isLogIn);
		$form = new Template('components/main/formAuthorization');
		$basket = $this->getBasketTemplate(Session::get('shoppingSession')->getProducts());

		$specialOfferPageTemplate = new Template('page/specialOffers/specialOffers', [
			'specialOffers' => $this->getSpecialOffersSectionTemplate($specialOffers),
			'form' => $form,
			'basket' => $basket,
		],);

		return (new Template('layout', [
			'header' => $header,
			'content' => $specialOfferPageTemplate,
			'footer' => $footer,
		]));
	}

	public function getHeaderTemplate(bool $isLogIn): Template
	{
		if ($isLogIn)
		{
			$authSection = new Template('components/main/logOut');
		}
		else
		{
			$authSection = new Template('components/main/logIn');
		}
		return new Template('components/main/header', ['authSection' => $authSection]);
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

	public function getSpecialOffersSectionTemplate(array $specialOffers): array
	{
		$specialOffersTemplates = [];
		foreach ($specialOffers as $specialOffer)
		{
			$specialOffersTemplates[] = new Template('components/specialOffers/specialOfferSection',
				[
					'description' =>$specialOffer->description,
					'title' => $specialOffer->title,
					'id' => $specialOffer->id,
				]
			);
		}
		return $specialOffersTemplates;
	}
}
