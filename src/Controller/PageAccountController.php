<?php

namespace Up\Controller;

use Up\Dto\UserDto;
use Up\Repository\User\UserRepositoryImpl;
use Up\Service\ProductService\ProductService;
use Up\Service\UserService\UserService;
use Up\Util\Session;
use Up\Util\TemplateEngine\PageAccountTemplateEngine;
use Up\Util\TemplateEngine\Template;

class PageAccountController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageAccountTemplateEngine();
	}

	public function indexAction(): void
	{
		if ($this->isLogIn())
		{
			$this->accountAction();
		}
		else
		{
			$this->logInAction();
		}
	}

	private function logInAction(): void
	{
		$this->engine->getAuthPageTemplate([
			'isLogIn' => $this->isLogIn()
		])->display();
	}

	public function accountAction(): void
	{
		$userId = Session::get('user')->id;

		$user = UserService::getUserById($userId);

		$dataUser = [
			'id' => $user->id,
			'email' => $user->email,
			'role' => $user->roleTitle,
			'phoneNumber' => $user->phoneNumber
		];

		$template = $this->engine->getPageTemplate([
			'isLogIn' => $this->isLogIn(),
			'user' => $dataUser
		]);

		$template->display();
	}

}