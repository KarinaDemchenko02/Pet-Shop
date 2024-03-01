<?php

namespace Up\Util\Middleware\PostMiddleware;

use Up\Http\Response;

class isAdminUnauthorized implements PostMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(Response $response, callable $next): Response
	{
		return $next($response);
	}
}
