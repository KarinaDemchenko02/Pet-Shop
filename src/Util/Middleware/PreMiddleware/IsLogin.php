<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Http\Request;
use Up\Http\Response;
use Up\Util\Session;

class IsLogin implements PreMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(Request $request, callable $next): Response
	{
		if (Session::get('logIn'))
		{
			$user = Session::get('user');
			$request->setData('user', $user);
		}
		else
		{
			$request->setData('user', null);
		}
		return $next($request);
	}
}
