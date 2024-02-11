<?php

namespace Up\Controller;

use Up\AdminAction\Change;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class ChangeController extends BaseController
{
	private Change $changeService;
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
		$this->changeService = new Change();
	}

	public function changeAction(): void
	{
		if (isset($_POST['changeProduct']))
		{
			$this->changeService->changeProduct(
				$_POST['id'],
				$_POST['title'],
				$_POST['desc'],
				$_POST['price']);

			echo 'product changed';

			foreach ($this->changeService->getErrors() as $error)
			{
				echo $error;
			}
		}
	}

}