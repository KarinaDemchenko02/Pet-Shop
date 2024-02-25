<?php

namespace Up\Util\Middleware\PreMiddleware;

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
		if (($user = $request->getDataByKey('user')) === null)
		{
			return new Response(Status::FORBIDDEN, ['redirect' => '/admin/logIn/']);
		}
		if ($user->roleTitle === 'Администратор')
		{
			return $next($request);
		}
		return new Response(Status::FORBIDDEN, ['redirect' => '/admin/logIn/']);
	}
}
