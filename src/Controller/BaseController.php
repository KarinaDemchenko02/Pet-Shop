<?php

namespace Up\Controller;


use Up\Service\Template;

abstract class BaseController
{
	public function render(string $templatePath, array $variables)
	{
		$template = new Template($variables);
		try
		{
			$template->setIncludeFile($templatePath);
			$template->display('layout');
		}
		catch (\Exception $exception)
		{
			echo "error";
		}
	}
}
