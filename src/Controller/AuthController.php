<?php

namespace Up\Controller;

use Up\Auth\Auth;
use Up\Auth\JwtService;
use Up\Auth\TokenType;
use Up\Dto\UserAddingDto;
use Up\Exceptions\User\UserNotFound;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Routing\Router;
use Up\Service\UserService\UserService;
use Up\Util\Session;
use Up\Util\TemplateEngine\PageMainTemplateEngine;

class AuthController extends Controller
{
	private Auth $authService;
	public function __construct()
	{
		Session::init();
		$this->authService = new Auth();
	}
	public function authAction(Request $request): Response
	{
		$action = $request->getDataByKey('action') . 'Action';
		try
		{
			return $this->$action($request);
		}
		catch (\Error)
		{
			return new Response(Status::UNAUTHORIZED, ['errors' => $this->authService->getErrors()]);
		}
	}

	public function logInAction(Request $request): Response
	{
		try
		{
			$user = UserService::getUserByEmail($request->getDataByKey('email'));
		}
		catch (UserNotFound)
		{
			$this->errors[] = 'Неправильно введён Email';
			return new Response(Status::UNAUTHORIZED, ['result' => false, 'errors' => $this->authService->getErrors()]);
		}

		if ($this->authService->verifyUser($user, $request->getDataByKey('password')))
		{
			$accessToken = JwtService::generateToken(
				TokenType::ACCESS,
				$user->email,
				$user->id,
				$user->roleTitle,
			);

			$refreshToken = JwtService::generateToken(
				TokenType::REFRESH,
				$user->email,
				$user->id,
				$user->roleTitle,
			);

			JwtService::saveTokenInCookie($accessToken, TokenType::ACCESS);
			JwtService::saveTokenInCookie($refreshToken, TokenType::REFRESH);

			Session::set('shoppingSession', ShoppingSessionRepositoryImpl::getByUser($user->id));
		}
		else
		{
			return new Response(Status::UNAUTHORIZED, ['result' => false,'errors' => $this->authService->getErrors()]);
		}
		return new Response(Status::OK, ['result' => true]);
	}

	/*public function logInAdminAction(Request $request): Response
	{
		try
		{
			$user = UserService::getUserByEmail($request->getDataByKey('email'));
			if ($this->authService->verifyUser($user, $request->getDataByKey('password')))
			{
				$token = JwtService::generateToken(['email' => $user->email, 'role' => $user->roleTitle, 'userId' => $user->id]);
				JwtService::saveTokenInCookie($token);
			}
			return new Response(Status::OK, ['redirect' => '/admin/']);
		}
		catch (UserNotFound)
		{
			$this->errors[] = 'Неправильно введён email или пароль';
		}
		return new Response(Status::UNAUTHORIZED);
	}*/

	private function registerAction(Request $request): Response
	{
		$user = new UserAddingDto(
			$request->getDataByKey('name'),
			$request->getDataByKey('surname'),
			$request->getDataByKey('email'),
			$request->getDataByKey('password'),
			$request->getDataByKey('phone'),
			2,
		);
		try
		{
			if (!$this->authService->registerUser($user))
			{
				$this->errors[] = $this->authService->getErrors();
				return new  Response(Status::BAD_REQUEST, ['result' => false, 'errors' => $this->errors]);
			}
		}
		catch (\Exception)
		{
			return new  Response(Status::BAD_REQUEST, ['result' => false, 'errors' => $this->errors]);
		}
		return new Response(Status::OK, ['result' => true, 'errors' => $this->errors]);
	}

	private function logOutAction(Request $request): Response
	{
		JwtService::deleteCookie(TokenType::ACCESS);
		JwtService::deleteCookie(TokenType::REFRESH);
		return new Response(Status::OK, ['result' => true]);
	}

}
