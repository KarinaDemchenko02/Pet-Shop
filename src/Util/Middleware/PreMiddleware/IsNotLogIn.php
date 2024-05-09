<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Auth\JwtService;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Util\Session;

class IsNotLogIn implements PreMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(Request $request, callable $next): Response
	{
		/*if ($request->getDataByKey('email') !== null)
		{
			JwtService::deleteCookie('jwt');
			return (new Response(Status::BAD_REQUEST, ['redirect' => '/admin/logIn/']));
		}*/

		return $next($request);
	}
}

class SomePreMiddleware implements PreMiddleware
{
	public function handle(Request $request, callable $next): Response
	{
		// Какой-то код
		return $next($request);
	}
}
