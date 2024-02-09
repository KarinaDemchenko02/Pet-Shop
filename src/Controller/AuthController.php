<?php

namespace Up\Controller;

use Up\Auth\Auth;
use Up\Util\TemplateEngine\PageMainTemplateEngine;
use Up\Util\TemplateEngine\Template;

class AuthController extends BaseController
{
	private Auth $authService;
	public function __construct()
	{
		$this->engine = new PageMainTemplateEngine();
		$this->authService = new Auth();
	}
	public function authAction()
	{
		if (isset($_POST['log_in']) && $this->authService->verifyUser($_POST['email'], $_POST['password']))
		{
			echo 'success log in';
		}
		if (isset($_POST['register']) && $this->authService->registerUser(
			$_POST['name'],
			$_POST['surname'],
			$_POST['phone'],
			$_POST['email'],
			$_POST['password'])
		)
		{
			echo 'success register';
		}

		foreach ($this->authService->getErrors() as $error)
		{
			echo $error;
		}
	}
}
