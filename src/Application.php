<?php

namespace Up;

use Up\Entity\ShoppingSession;
use Up\Http\Request;
use Up\Http\Response;
use Up\Routing\Router;
use Up\Util\Session;

class Application
{
	private Router $router;

	/**
	 * @var array<string, class-string|string>
	 */
	private array $routeMiddleware = [
		'isLogin' => \Up\Util\Middleware\PreMiddleware\IsLogin::class,
		'isAdmin' => \Up\Util\Middleware\PreMiddleware\IsAdmin::class,
		'isNotLogIn' => \Up\Util\Middleware\PreMiddleware\IsNotLogIn::class,
	];
	private array $middlewarePriority = [
		\Up\Util\Middleware\PreMiddleware\IsLogin::class,
		\Up\Util\Middleware\PreMiddleware\IsAdmin::class,
		\Up\Util\Middleware\PreMiddleware\IsNotLogIn::class,
	];
	private array $postMiddleware = [
		'test' => \Up\Util\Middleware\PostMiddleware\Test::class
	];
	private array $postMiddlewarePriority = [
		'test' => \Up\Util\Middleware\PostMiddleware\Test::class
	];

	public function __construct()
	{
		$this->router = new Router();

		$this->router->setPreMiddlewarePriority($this->middlewarePriority);
		$this->router->setPostMiddlewarePriority($this->postMiddlewarePriority);

		foreach ($this->routeMiddleware as $key => $middleware)
		{
			$this->router->aliasPreMiddleware($key, $middleware);
		}

		foreach ($this->postMiddleware as $key => $middleware)
		{
			$this->router->aliasPostMiddleware($key, $middleware);
		}
	}

	public function run(): void
	{
		Util\Database\Migration::migrate();

		$this->compressImages();

		$this->initSession();

		$request = $this->handleRequest();
		$response = $this->sendRequestThroughRouter($request);
		$this->sendResponse($response);
	}

	private function sendRequestThroughRouter(Request $request): Response
	{
		return $this->router->run($request);
	}

	private function handleRequest(): Request
	{
		$method = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];

		return (new Request($method, $uri));
	}

	private function sendResponse(Response $response): void
	{
		echo $response;
		if ($destination = $response->getDataByKey('redirect'))
		{
			Router::redirect($destination);
		}
	}

	private function compressImages(): void
	{
		$imagesCompressed = new Util\Compression\CompressionImages
		(
			ROOT . '/public/images/',
			ROOT . '/public/compressImages/');

		$imagesCompressed->compressImages();
	}

	private function initSession(): void
	{
		Session::init();
		$user = Session::get('user');
		$shoppingSession = Session::get('shoppingSession');
		if (!isset($shoppingSession) && is_null($user))
		{
			Session::set('shoppingSession', new ShoppingSession(null, null, []));
		}
	}

	// private function setHeaders(): void
	// {
	// 	header("Access-Control-Allow-Origin: /");
	// 	/*header("Content-Type: application/json; charset=UTF-8");*/
	// 	header("Access-Control-Allow-Methods: POST");
	// 	header("Access-Control-Max-Age: 3600");
	// 	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	// }
}
