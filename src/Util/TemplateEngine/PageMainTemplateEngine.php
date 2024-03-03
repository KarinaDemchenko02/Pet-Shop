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
		$basketItems = Session::get('shoppingSession')->getProducts();

		$basketItemsTemplates = [];
		foreach ($basketItems as $item)
		{
			$basketItemsTemplates[] = [
				'title' => $item->info->title,
				'price' => $item->info->price,
				'id' => $item->info->id,
				'imagePath' => $item->info->imagePath,
				'quantity' => $item->getQuantity(),
			];
		}

		$footer = new Template('components/main/footer');
		$header = new Template('components/main/header', [
			'basketItem' => $basketItemsTemplates,
			'products' => $products,
		]);

		$form = new Template('components/main/formAuthorization');
		$pagination = new Template('components/main/pagination', [
			'products' => $products,
			'nextPage' => $nextPage
		]);



		$basketModal = new Template('components/main/basket', ['items' => $basketItemsTemplates]);

		$mainPageTemplate = new Template('page/main/main', [
			'tag' => $tags,
			'products' => $products,
			'form' => $form,
			'basket' => $basketModal,
			'isLogIn' => $isLogIn,
			'basketItem' => $basketItemsTemplates,
			'pagination' => $pagination,
		],);

		return (new Template('layout', [
			'header' => $header,
			'content' => $mainPageTemplate,
			'footer' => $footer,
		]));
	}

}
