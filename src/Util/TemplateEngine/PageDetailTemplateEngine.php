<?php

namespace Up\Util\TemplateEngine;

use Up\Dto\ProductDto;

class PageDetailTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(ProductDto $productDto): Template
	{
		$detailTemplate = new Template('page/detail/detail', [
			'title' => $productDto->title,
			'desc' => $productDto->description,
			'price' => $productDto->price,
			'id' => $productDto->id,
		]);
		return (new Template('layout', ['content' => $detailTemplate]));
	}
}
