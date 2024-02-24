<?php

namespace Up\Routing;

class Router
{
	/**
	 * @var Route[]
	 */
	private static array $routes = [];
	private static function add(string $method, string $uri, object $controller, string $action):void
	{
		$class = $controller::class;
		if (method_exists($class, $action) && !isset(self::$routes["$class$action"]))
		{
			self::$routes["$class$action"] = new Route(
				$method,
				$uri,
				function(...$variables) use ($controller, $action)
				{
					return $controller->$action(...$variables);
				}
			);
		}
	}

	public static function get(string $uri, object $controller, string $action)
	{
		self::add('GET', $uri, $controller, $action);
	}
	public static function post(string $uri, object $controller, string $action)
	{
		self::add('POST', $uri, $controller, $action);
	}
	public static function put(string $uri, object $controller, string $action)
	{
		self::add('PUT', $uri, $controller, $action);
	}
	public static function patch(string $uri, object $controller, string $action)
	{
		self::add('PATCH', $uri, $controller, $action);
	}
	public static function delete(string $uri, object $controller, string $action)
	{
		self::add('DELETE', $uri, $controller, $action);
	}
	public static function find(string $REQUEST_METHOD, string $REQUEST_URI): Route|false
	{
		foreach (self::$routes as $route)
		{

			if ($route->match($REQUEST_URI) && $route->method === $REQUEST_METHOD)
			{
				return $route;
			}
		}

		return false;
	}
}
