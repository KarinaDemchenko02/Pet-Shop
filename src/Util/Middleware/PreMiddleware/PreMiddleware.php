<?php

namespace Up\Util\Middleware\PreMiddleware;

use Up\Http\Request;
use Up\Http\Response;
use Up\Util\Middleware\Middleware;

interface PreMiddleware extends Middleware
{
	/**
	 * @param Request $request
	 * @param callable(Request):Response $next
	 * @return Response
	 */
	public function handle(Request $request, callable $next): Response;
}
