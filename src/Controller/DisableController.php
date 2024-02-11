<?php

namespace Up\Controller;

use Up\AdminAction\Disable;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;
use function PHPUnit\Framework\isEmpty;

class DisableController extends BaseController
{
	private Disable $disableService;
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
		$this->disableService = new Disable();
	}

	public function disableAction(): void
	{
		if (isset($_POST['disable'])) {
			$this->disableService->disableProduct(((int) $_POST['id']));

			echo 'product delete';

			foreach ($this->disableService->getErrors() as $error)
			{
				echo $error;
			}
		}
	}
}