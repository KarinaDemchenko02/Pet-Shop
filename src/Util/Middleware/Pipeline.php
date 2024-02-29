<?php

namespace Up\Util\Middleware;

use Up\Http\Passable;
use Up\Http\Request;
use Up\Http\Response;

final class Pipeline
{

	/**
	 * @param list<Middleware> $middlewares
	 */
	private array $middlewares = [];
	private mixed $passable;
	private \Closure $destination;
	private string $method = 'handle';

	public function send(Passable $passable): Pipeline
	{
		$this->passable = $passable;
		return $this;
	}

	public function through(array|string|null $middlewares): Pipeline
	{
		if (is_null($middlewares))
		{
			return $this;
		}

		if (!is_array($middlewares))
		{
			$this->middlewares[] = new $middlewares;
			return $this;
		}

		foreach ($middlewares as $middleware)
		{
			$this->middlewares[] = new $middleware;
		}

		return $this;
	}

	public function then(\Closure $destination): Response
	{
		$this->destination = $destination;
		return $this->handle();
	}


	public function thenReturn(): Response
	{
		$this->destination = static function ($passable) {
			return $passable;
		};
		return $this->handle();
	}

	public function handle(): Response
	{
		$middleware = array_shift($this->middlewares);
		if (!is_null($middleware))
		{
			return $middleware->handle($this->passable, [$this, $this->method]);
		}
		return ($this->destination)($this->passable);
	}
}
