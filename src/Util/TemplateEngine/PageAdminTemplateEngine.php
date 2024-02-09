<?php

namespace Up\Util\TemplateEngine;

class PageAdminTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$products = $variables['products'];
		$users = $variables['users'];
		$tags = $variables['tags'];

		$header = new Template('components/admin/header');
		$table = new Template('components/admin/table');

		return (new Template('page/admin/admin', [
			'header' => $header,
			'table' => $table
		]));
	}

}