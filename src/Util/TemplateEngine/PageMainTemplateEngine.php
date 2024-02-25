<?php

namespace Up\Util\TemplateEngine;

use Up\Util\Session;

class PageMainTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$products = $variables['products'];
		$tags = $variables['tag'];
		$isLogIn = $variables['isLogIn'];
		$nextPage = $variables['nextPage'];

		$footer = new Template('components/main/footer');
		$header = new Template('components/main/header');
		$form = new Template('components/main/formAuthorization');
		$basket = $this->getBasketTemplate(Session::get('shoppingSession')->getProducts());
		$pagination = new Template('components/main/pagination', [
			'products' => $products,
			'nextPage' => $nextPage
		]);

		$mainPageTemplate = new Template('page/main/main', [
			'tag' => $tags,
			'products' => $products,
			'form' => $form,
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
}
