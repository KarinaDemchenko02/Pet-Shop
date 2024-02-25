<?php

namespace Up\Http;
class Request implements Passable
{
	public readonly string $method;
	public readonly string $uri;
	private mixed $data;
	private array $variables = [];
	public function __construct(string $method, string $uri)
	{
		$this->method = $method;
		$this->uri = $uri;
		$this->data = $this->handleData();
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
			return $_POST;
		}
	}

	public function getVariables(): array
	{
		return $this->variables;
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
