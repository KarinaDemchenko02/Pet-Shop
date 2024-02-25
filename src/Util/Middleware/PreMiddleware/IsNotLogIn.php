<?php

namespace Up\Util\Middleware\PreMiddleware;

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
		if (Session::get('logIn'))
		{
			Session::delete();
			return (new Response(Status::BAD_REQUEST, ['redirect' => '/admin/logIn/']));
		}

		return $next($request);
	}
}
