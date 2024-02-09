<?php

namespace Up\Util\TemplateEngine;

use Up\Dto\ProductDto;

class PageDetailTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(ProductDto $productDto): Template
	{
        $form = new Template('components/main/formAuthorization');
        $formBuyProduct = new Template('components/detail/formBuyProduct', [
            'title' => $productDto->title,
            'price' => $productDto->price,
        ]);
        $basket = new Template('components/main/basket');

		$detailTemplate = new Template('page/detail/detail', [
			'title' => $productDto->title,
			'desc' => $productDto->description,
			'price' => $productDto->price,
			'id' => $productDto->id,
            'form' => $form,
            'formBuyProduct' => $formBuyProduct,
            'basket' => $basket
		]);
		$footer = new Template('components/main/footer');
		$header = new Template('components/main/header');

		return (new Template('layout', [
			'header' => $header,
			'content' => $detailTemplate,
			'footer' => $footer,
		]));
	}
}
