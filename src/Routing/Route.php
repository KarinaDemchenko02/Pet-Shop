<?php

namespace Up\Routing;

class Route
{
	private array $variables = [];
	public function __construct(
		public readonly string $method,
		public readonly string $uri,
		public readonly \Closure $action,
	){}

	public function match(string $uri): bool
	{
		$regexpVar = '([A-Za-z0-9_-]+)';
		$regexp = '#^' . preg_replace('(:[A-Za-z]+)', $regexpVar, $this->uri) . '$#';

		$matches = [];
		$result = preg_match($regexp, $uri, $matches);

		if ($result)
		{
			array_shift($matches);
			$this->variables = $matches;
		}

		return $result;
	}

	public function getVariables(): array
	{
		return $this->variables;
	}
}
