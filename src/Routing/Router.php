<?php

namespace Up\Routing;

use Up\Controller\RedirectController;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Util\Middleware\Pipeline;

class Router
{
	/**
	 * @var Route[]
	 */
	private static array $routes = [];

	private array $preMiddleware = [];
	private array $preMiddlewarePriority = [];
	private array $postMiddleware = [];
	private array $postMiddlewarePriority = [];
	private Request $currentRequest;

	/**
	 * @param string|array $method
	 * @param string $uri
	 * @param object $controller
	 * @param string $action
	 * @return Route
	 */
	private static function add(string|array $method, string $uri, object $controller, string $action): Route
	{
		$class = $controller::class;
		if (method_exists($class, $action) && !isset(self::$routes["$class$action"]))
		{
			$route = new Route(
				$method,
				$uri,
				function(Request $request) use ($controller, $action)
				{
					return $controller->$action($request);
				}
			);
			self::$routes["$class$action"] = $route;
		}
		return @$route;
	}

	public static function get(string $uri, object $controller, string $action): Route
	{
		return self::add('GET', $uri, $controller, $action);
	}
	public static function post(string $uri, object $controller, string $action): Route
	{
		return self::add('POST', $uri, $controller, $action);
	}
	public static function put(string $uri, object $controller, string $action): Route
	{
		return self::add('PUT', $uri, $controller, $action);
	}
	public static function patch(string $uri, object $controller, string $action): Route
	{
		return self::add('PATCH', $uri, $controller, $action);
	}
	public static function delete(string $uri, object $controller, string $action): Route
	{
		return self::add('DELETE', $uri, $controller, $action);
	}

	public static function any(string $uri, object $controller, string $action): Route
	{
		$verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
		return self::add($verbs, $uri, $controller, $action);
	}

	public function run(Request $request): Response
	{
		if ($route = self::find($request))
		{
			$action = $route->action;
			$variables = array_merge($route->getVariables(), $route->getParams());
			$request->setVariables($variables);
			$middlewares = $this->getSortedMiddlewares($route->getPreMiddlewares(), 'pre');

			$response = (new Pipeline())
				->send($request)
				->through($middlewares)
				->then($action);
		}
		else
		{
			$data = ['errors' => 'Page not found'];
			return new Response(Status::NOT_FOUND, $data);
		}
		$postMiddlewares = $this->getSortedMiddlewares($route->getPostMiddlewares(), 'post');
		$response = (new Pipeline())
			->send($response)
			->through($postMiddlewares)
			->thenReturn();
		return $response;
	}

	public function aliasPreMiddleware(string $name, string $class): void
	{
		$this->preMiddleware[$name] = $class;
	}
	public function aliasPostMiddleware(string $name, string $class): void
	{
		$this->postMiddleware[$name] = $class;
	}

	private function getSortedMiddlewares(array|string $toSortMiddlewares, string $prefix): array|string|null
	{
		if ($prefix !== 'pre' && $prefix !== 'post' && $toSortMiddlewares === [])
		{
			return [];
		}
		$middlewaresName = $prefix . 'Middleware';
		$middlewaresPriority = $prefix . 'MiddlewarePriority';
		return $this->sortMiddleware($this->$middlewaresName, $toSortMiddlewares, $this->$middlewaresPriority);
	}

	public function sortMiddleware(array|string $middlewaresName, array|string $middlewares, array|string $middlewaresPriority): array|string|null
	{
		if (is_string($middlewaresName))
		{
			return $middlewares[$middlewaresName];
		}
		$result = [];
		foreach ($middlewares as $middleware)
		{
			if (isset($middlewaresName[$middleware]))
			{
				$result[] = $middlewaresName[$middleware];
			}
		}

		if (empty($result))
		{
			return null;
		}

		uasort($result, function ($a, $b) use ($middlewaresPriority) {
			if (array_search($a, $middlewaresPriority, true) >
				array_search($b, $middlewaresPriority, true))
			{
				return 1;
			}
			return -1;
		});

		return $result;
	}

	/**
	 * @param array<string, mixed> $attributes
	 * @param array<Route> $routes
	 * @return void
	 */
	public static function group(array $attributes, array $routes): void
	{
		foreach ($routes as $route)
		{
			self::setAttributes($attributes, $route);
		}
	}

	/**
	 * @param array<string, mixed> $attributes
	 * @param Route $route
	 * @return void
	 */
	public static function setAttributes(array $attributes, Route $route): void
	{
		$methodName = key($attributes);
		foreach ($attributes as $attribute)
		{
			$variable = $attribute;
			$route->$methodName($variable);
		}
	}

	public static function redirect(string $destination): void
	{

		header('Location: ' . $destination);
		/*return self::any($uri, new RedirectController(), 'redirect')
			->setDefault('destination', $destination)
			->setDefault('status', $status);*/
	}

	public static function find(Request $request): Route|false
	{
		foreach (self::$routes as $route)
		{
			if ($route->match($request->uri) && $route->methods === $request->method)
			{
				return $route;
			}
		}

		return false;
	}

	/**
	 * @param array $preMiddlewarePriority
	 */
	public function setPreMiddlewarePriority(array $preMiddlewarePriority): void
	{
		$this->preMiddlewarePriority = $preMiddlewarePriority;
	}
	/**
	 * @param array $postMiddlewarePriority
	 */
	public function setPostMiddlewarePriority(array $postMiddlewarePriority): void
	{
		$this->postMiddlewarePriority = $postMiddlewarePriority;
	}
}
