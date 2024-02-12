<?php

namespace Up\Controller;

use Up\AdminAction\Add;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class AddController extends BaseController
{
	private Add $addService;
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
		$this->addService = new Add();
	}

	public function addAction(): void
	{
		if (isset($_POST['add']))
		{
			$this->addService->addProduct($_POST['title'], $_POST['desc'], $_POST['price'], $_POST['tag']);
		}

		foreach ($this->addService->getErrors() as $error)
		{
			echo $error;
		}
	}
}
