<?php

namespace Up\Util\Middleware\PostMiddleware;

use Up\Http\Response;
use Up\Util\Middleware\Middleware;

interface PostMiddleware extends Middleware
{
	/**
	 * @param Response $response
	 * @param callable(Response):Response $next
	 * @return Response
	 */
	public function handle(Response $response, callable $next): Response;
}
