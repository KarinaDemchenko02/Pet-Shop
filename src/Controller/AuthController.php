<?php

namespace Up\Controller;

use Up\Auth\Auth;
use Up\Dto\UserAddingDto;
use Up\Exceptions\User\UserNotFound;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Service\UserService\UserService;
use Up\Util\Json;
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
		$data = Json::decode(file_get_contents("php://input"));

		if ($data['action'] === 'logOut')
		{
			$this->logOut();
		}
		if ($data['action'] === 'logIn')
		{
			$this->logInAction($data['email'], $data['password']);
		}
		if ($data['action'] === 'register')
		{
			$user = new UserAddingDto(
				$data['name'],
				$data['surname'],
				$data['email'],
				$data['password'],
				$data['phone'],
				'Пользователь',
			);
			$this->register($user);
		}

		$this->errors = array_merge($this->errors, $this->authService->getErrors());
	}

	private function logInAction(string $email, string $password): void
	{
		$response = [];

		try
		{
			$user = UserService::getUserByEmail($email);
			$result = true;
		}
		catch (UserNotFound)
		{
			$this->errors[] = 'Неправильно введён Email';
			$result = false;
		}

		if ($this->authService->verifyUser($user, $password))
		{
			Session::set('logIn', true);
			Session::set('user', $user);
			Session::set('shoppingSession', ShoppingSessionRepositoryImpl::getByUser($user->id));
		}

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'User not auth';
			http_response_code(409);
		}
		echo Json::encode($response);
		exit();
	}

	public function logInAdminAction(): void
	{
		try
		{
			$user = UserService::getUserByEmail($_POST['email']);
			if ($this->authService->verifyUser($user, $_POST['password']))
			{
				Session::set('logIn', true);
				Session::set('user', $user);
			}
		}
		catch (UserNotFound)
		{
			$this->errors[] = 'Неправильно введён Email';
		}
		finally
		{
			header('Location: /admin/');
		}
	}

	private function register(UserAddingDto $user): void
	{
		if (!$this->authService->registerUser($user))
		{
			$this->errors[] = 'Не удалось добавить пользователя';
			$result = false;
			return;
		}

		$result = true;

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'User not auth';
			http_response_code(409);
		}

		echo Json::encode($response);

		exit();
	}

	private function logOut(): void
	{
		Session::delete();
		self::$user = null;

		$result = true;

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'User not auth';
			http_response_code(409);
		}
		echo Json::encode($response);

		exit();
	}
}
