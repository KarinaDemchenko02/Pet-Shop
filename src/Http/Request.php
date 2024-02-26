<?php

namespace Up\Http;
class Request implements Passable
{
	public readonly string $method;
	public readonly string $uri;
	private mixed $data;
	private array $headers;

	private array $variables = [];
	private array $cookies;

	public function getCookie(string $name): string
	{
		if (($cookie = @$this->cookies[$name]) !== null)
		{
			return $cookie;
		}
		return '';
	}
	public function __construct(string $method, string $uri)
	{
		$this->method = $method;
		$this->uri = $uri;
		$this->data = $this->handleData();
		$this->headers = $this->handleHeaders();
		$this->cookies = $this->handleCookies();
	}
	/**
	 * @return mixed
	 */
	public function getData(): mixed
	{
		return $this->data;
	}

	public function setData(string $key, mixed $value): void
	{
		$this->data[$key] = $value;
	}
	public function getDataByKey(string $key): mixed
	{
		return $this->data[$key] ?? null;
	}
	private function handleData(): mixed
	{
		if ($this->method === 'GET')
		{
			return null;
		}
		try
		{
			if (($data = $this->fromJson()) !== null)
			{
				return $data;
			}
			return $_POST;
		}
		catch (\JsonException)
		{
			if (($data = $this->handleImage()) !== null)
			{
				return array_merge($_POST, ['image' => $data]);
			}
			return $_POST;
		}
	}

	private function handleImage(): mixed
	{
		return $_FILES['imagePath'] ?? null;
	}

	private function handleCookies(): array
	{
		return $_COOKIE ?? [];
	}

	private function handleHeaders(): array
	{
		$headers = [];
		foreach($_SERVER as $key => $value) {
			if (!str_starts_with($key, 'HTTP_'))
			{
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$headers[$header] = $value;
		}
		return $headers;
	}

	public function getVariables(): array
	{
		return $this->variables;
	}

	/**
	 * @return array
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getVariable(string $variableName): mixed
	{
		return $this->variables[$variableName] ?? null;
	}

	public function setVariables(array $variables): void
	{
		$this->variables = $variables;
	}

	/**
	 * @throws \JsonException
	 */
	private function fromJson(): mixed
	{
		$rawData = file_get_contents("php://input");
		return json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
	}
}
