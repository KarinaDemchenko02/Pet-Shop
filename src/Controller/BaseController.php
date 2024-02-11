<?php

namespace Up\Controller;


use Up\Dto\UserDto;
use Up\Util\Session;
use Up\Util\TemplateEngine\TemplateEngine;

abstract class BaseController
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
}
