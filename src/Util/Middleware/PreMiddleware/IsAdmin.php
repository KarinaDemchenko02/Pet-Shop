<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Auth\JwtService;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;

class IsAdmin implements PreMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(Request $request, callable $next): Response
	{
		if ($request->getDataByKey('role') === 'Администратор')
		{
			return $next($request);
		}
		JwtService::deleteCookie('jwt');
		return new Response(Status::SEE_OTHER, ['redirect' => '/admin/logIn/']);
	}
}
