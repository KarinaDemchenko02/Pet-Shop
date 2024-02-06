<?php

namespace Up\Util\TemplateEngine;

class PageMainTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$products = $variables['products'];
		$tags = $variables['tags'];

		$footer = new Template('components/main/footer');
		$header = new Template('components/main/header');
        $form = new Template('components/main/FormAuthorization');

		$mainPageTemplate = new Template('page/main/main', [
			'tags' => $this->getTagsSectionTemplate($tags),
			'products' => $this->getProductsSectionTemplate($products),
            'form' => $form,
		],);

		return (new Template('layout', [
			'header' => $header,
			'content' => $mainPageTemplate,
			'footer' => $footer,
		]));
	}
	public function getTagsSectionTemplate(array $tags): array
	{
		$tagsTemplates = [];
		foreach ($tags as $tag)
		{
			$tagsTemplates[] = new Template('components/main/tag',
				[
					'title' => $tag->title,
					'id' => $tag->id,
				]
			);
		}
		return $tagsTemplates;
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
				]
			);
		}
		return $productTemplates;
	}
}
