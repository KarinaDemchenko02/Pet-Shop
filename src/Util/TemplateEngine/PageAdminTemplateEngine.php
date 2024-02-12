<?php

namespace Up\Util\TemplateEngine;

class PageAdminTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$products = $variables['products'];
		$columnsProducts = $variables['columnsProducts'];

		$header = new Template('components/admin/header');
		$form = new Template('components/admin/form');
		$delete = new Template('components/admin/delete');

		$content = new Template('components/admin/table', [
			'columnsProducts' => $columnsProducts,
			'products' => $this->getProductsSectionTemplate($products),
			'form' => $form,
			'delete' => $delete
		]);

		return (new Template('page/admin/admin', [
			'header' => $header,
			'content' => $content
		]));
	}
	public function getAuthPageTemplate(): Template
	{
		$header = new Template('components/admin/header');
		$content = new Template('components/admin/formAuthorization');
		return (new Template('page/admin/admin', [
			'header' => $header,
			'content' => $content,
		]));
	}
	public function getProductsSectionTemplate(array $products): array
	{
		$productTemplates = [];
		foreach ($products as $product)
		{
			$productTemplates[] = new Template('components/admin/dataTable',
				[
					'title' => $product->title,
					'desc' => $product->description,
					'price' => $product->price,
					'id' => $product->id,
					'isActive' => (int) $product->isActive,
					'addedAt' => $product->addedAt,
					'editedAt' => $product->editedAt
				]
			);
		}

		return $productTemplates;
	}
}
