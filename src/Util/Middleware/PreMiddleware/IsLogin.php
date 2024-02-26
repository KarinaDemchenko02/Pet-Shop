<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Auth\JwtService;
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
		$data = [];
		if (($jwt = $request->getCookie('jwt')) !== '')
		{
			$data = (JwtService::validateToken($jwt))['data'];
		}

		if (!empty($data))
		{
			$request->setData('email', $data['email']);
			$request->setData('role', $data['role']);
		}
		else
		{
			$request->setData('email', null);
			$request->setData('role', null);
		}

		return $next($request);
	}
}
