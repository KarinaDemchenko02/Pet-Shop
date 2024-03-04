<?php

namespace Up\Util\TemplateEngine;

use Up\Util\Session;

class PageSpecialOfferIdTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$specialOfferTitle = $variables['specialOfferTitle'];
		$products = $variables['products'];
		$isLogIn = $variables['isLogIn'];
		$nextPage = $variables['nextPage'];

		$footer = new Template('components/main/footer');
		$header = $this->getHeaderTemplate($isLogIn);
		$form = new Template('components/main/formAuthorization');
		$basket = $this->getBasketTemplate(Session::get('shoppingSession')->getProducts());
		$pagination = new Template('components/main/pagination', [
			'products' => $products,
			'nextPage' => $nextPage
		]);

		$mainPageTemplate = new Template('page/specialOffers/products', [
			'specialOfferTitle'=>$specialOfferTitle,
			'products' => $this->getProductsSectionTemplate($products),
			'form' => $form,
			'isLogIn' => $isLogIn,
			'basket' => $basket,
			'pagination' => $pagination
		],);

		return (new Template('layout', [
			'header' => $header,
			'content' => $mainPageTemplate,
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

	public function getProductsSectionTemplate(array $products): array
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
