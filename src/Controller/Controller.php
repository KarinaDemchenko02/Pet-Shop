<?php

namespace Up\Controller;


use Up\Dto\User\UserDto;
use Up\Util\Session;
use Up\Util\TemplateEngine\TemplateEngine;

abstract class Controller
{
	protected TemplateEngine $engine;
	protected array $errors = [];

	protected static ?UserDto $user = null;

	protected function isLogIn(): bool
	{
		if (Session::get('logIn'))
		{
			self::$user = Session::get('user');
			return true;
		}
		return false;
	}
	protected function getUser()
	{
		return self::$user;
	}
	protected function isLogInAdmin(): bool
	{
		if (!Session::get('logIn'))
		{
			return false;
		}
		self::$user = Session::get('user');
		return !is_null(self::$user) && self::$user->roleTitle === 'Администратор';
	}
}
