<?php

namespace Up\Controller;

use Up\Auth\Auth;
use Up\Dto\UserAddingDto;
use Up\Exceptions\Service\UserService\UserNotFound;
use Up\Service\UserService\UserService;
use Up\Util\Session;
use Up\Util\TemplateEngine\PageMainTemplateEngine;

class AuthController extends BaseController
{
	private Auth $authService;
	public function __construct()
	{
		Session::init();
		$this->engine = new PageMainTemplateEngine();
		$this->authService = new Auth();
	}
	public function authAction()
	{
		if (isset($_POST['logOut']))
		{
			$this->logOut();
		}
		if (isset($_POST['logIn']))
		{
			$this->logIn();
		}
		if (isset($_POST['register']))
		{
			$this->register();
		}

		$this->errors = array_merge($this->errors, $this->authService->getErrors());

		header("Location: {$_SERVER['REQUEST_URI']}");
	}

	private function logIn(): void
	{
		try
		{
			$user = UserService::getUserByEmail($_POST['email']);
		}
		catch (UserNotFound)
		{
			$this->errors[] = 'Неправильно введён Email';
			return;
		}

		if ($this->authService->verifyUser($user, $_POST['password']))
		{
			Session::set('logIn', true);
			Session::set('user', $user);
		}
	}

	private function register(): void
	{
		$user = new UserAddingDto(
			$_POST['name'],
			$_POST['surname'],
			$_POST['phone'],
			$_POST['email'],
			$_POST['password'],
			'Пользователь',
		);

		if (!$this->authService->registerUser($user))
		{
			$this->errors[] = 'Не удалось добавить пользователя';
		}
	}

	private function logOut(): void
	{
		Session::delete();
		self::$user = null;
	}
}
