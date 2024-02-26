<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Auth\JwtService;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
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
			$jwt =  (JwtService::validateToken($jwt));
		}

		if ($jwt === '')
		{
			JwtService::deleteCookie('jwt');
		}
		else
		{
			$data = @$jwt['data'];
		}

		if (!empty($data))
		{
			$request->setData('email', $data['email']);
			$request->setData('role', $data['role']);
			@$request->setData('userId', $data['userId']);
		}
		else
		{
			$request->setData('email', null);
			$request->setData('role', null);
			$request->setData('userId', null);
		}
		return $next($request);
	}
}
