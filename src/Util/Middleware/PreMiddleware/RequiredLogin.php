<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Auth\JwtService;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;

class RequiredLogin implements PreMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(Request $request, callable $next): Response
	{
		if ($request->getCookie('jwt') === '')
		{
			return new Response(Status::UNAUTHORIZED, ['redirect' => '/']);
		}
		return $next($request);
	}
}
