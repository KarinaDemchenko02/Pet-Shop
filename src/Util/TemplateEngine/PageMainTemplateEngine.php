<?php

namespace Up\Util\TemplateEngine;

class PageMainTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $products): Template
	{
		$productTemplates = [];
		foreach ($products as $product) {
			$productTemplates[] = new Template('components/main/product',
				[
					'title' => $product->title,
					'desc' => $product->description,
					'id' => $product->id,
				]
			);
		}
		$mainPageTemplate = new Template('page/main/main', ['products' => $productTemplates],);
		return (new Template('layout', [
			'content' => $mainPageTemplate,
		]));
	}
}
