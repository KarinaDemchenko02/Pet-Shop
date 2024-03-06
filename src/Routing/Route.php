<?php

namespace Up\Routing;

class Route
{
	/**
	 * @var array
	 */
	private array $variables = [];

	private array $getParams = [];

	/**
	 * @var array
	 */
	public array $defaults = [];

	/**
	 * @var array
	 */
	public array $wheres = [];
	public readonly array|string $methods;
	public readonly string $uri;
	public \Closure $action;
	public array $preMiddlewares = [];
	public array $postMiddlewares = [];
	public string $redirectDestination = '';

	/**
	 * @param array|string $methods
	 * @param string $uri
	 * @param \Closure $action
	 */
	public function __construct(array|string $methods, string $uri, \Closure $action)
	{
		$this->methods = $methods;
		$this->uri = $uri;
		$this->action = $action;
	}

	public function match(string $uri): bool
	{

		@[$uri, $getParams] = explode('?', $uri);

		$this->setGetParams($getParams);

		$regexpVar = '([A-Za-z0-9_-]+)';

		$variables = [];
		$count = preg_match('(:[A-Za-z]+)', $this->uri, $variables);

		$variables = array_map(callback: static function ($variable) {
			return substr($variable, 1);
		}, array: $variables);

		$regexp = '#^' . preg_replace('(:[A-Za-z]+)', $regexpVar, $this->uri) . '$#';
		$matches = [];
		$result = preg_match($regexp, $uri, $matches);
		if ($result)
		{
			array_shift($matches);
			for($i = 0; $i < $count; $i++)
			{
				$this->variables[$variables[$i]] = $matches[$i];
			}
		}
		return $result;
	}
	public function getParams(): array
	{
		return $this->getParams;
	}
	private function setGetParams(?string $params): void
	{
		if (is_null($params))
		{
			return;
		}

		$paramArray = explode('&', $params);
		foreach ($paramArray as $param)
		{
			[$name, $value] = explode('=', $param);
			if (is_numeric($value))
			{
				$value = (int)$value;
			}
			$this->getParams[$name] = $value;
		}
	}
	public function preMiddleware(array|string $middleware): static
	{
		if (!is_array($middleware))
		{
			$this->preMiddlewares[] = $middleware;
		}
		else
		{
			$this->preMiddlewares = array_merge($this->preMiddlewares ?? [], $middleware);
		}

		return $this;
	}
	public function postMiddleware(array|string $middleware): static
	{
		if (!is_array($middleware))
		{
			$this->postMiddlewares[] = $middleware;
		}
		else
		{
			$this->postMiddlewares = array_merge($this->postMiddlewares ?? [], $middleware);
		}
		return $this;
	}
	public function redirect(string $destination): static
	{
		/*if (!filter_var($destination, FILTER_VALIDATE_URL))*/
		{
			$this->redirectDestination = $destination;
		}

		return $this;
	}
	public function getPreMiddlewares(): array
	{
		return $this->preMiddlewares;
	}
	public function getPostMiddlewares(): array
	{
		return $this->postMiddlewares;
	}
	/*public function where(): Route
	{
		// TODO implement the method
	}*/
	public function getVariables(): array
	{
		return $this->variables;
	}

	/**
	 * @return array
	 */
	public function getDefaults(): array
	{
		return $this->defaults;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return Route
	 */
	public function setDefault(string $name, mixed $value): Route
	{
		$this->defaults[$name] = $value;
		return $this;
	}
}
