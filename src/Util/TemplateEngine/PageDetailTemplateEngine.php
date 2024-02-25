<?php

namespace Up\Util\TemplateEngine;

use Up\Util\Session;

class PageDetailTemplateEngine implements TemplateEngine
{
	public function getPageTemplate($variables): Template
	{
		$productDto = $variables['productDto'];

		$form = new Template('components/main/formAuthorization');
		$formBuyProduct = new Template('components/detail/formBuyProduct', [
			'title' => $productDto->title,
			'price' => $productDto->price,
			'id' => $productDto->id,
			'imagePath' => $productDto->imagePath,
		]);
		$basket = $this->getBasketTemplate(Session::get('shoppingSession')->getProducts());

		$detailTemplate = new Template('page/detail/detail', [
			'title' => $productDto->title,
			'desc' => $productDto->description,
			'price' => $productDto->price,
			'id' => $productDto->id,
			'imagePath' => $productDto->imagePath,
			'form' => $form,
			'formBuyProduct' => $formBuyProduct,
			'basket' => $basket,
			'characteristics' =>$productDto->characteristics,
		]);
		$footer = new Template('components/main/footer');
		$isLogIn = $variables['isLogIn'];

		$header = $this->getHeaderTemplate($isLogIn);

		return (new Template('layout', [
			'header' => $header,
			'content' => $detailTemplate,
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

	public function viewModalSuccess(): Template
	{
		return new Template('components/modals/success');
	}
}
