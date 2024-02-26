<?php

namespace Up\Http;

final class Response implements Passable
{
	private readonly Status $status;
	private mixed $data;
	public function __construct(Status $status, mixed $data = [])
	{
		$this->data = $data;
		$this->status = $status;
	}

	/**
	 * @throws \JsonException
	 */
	public function __toString(): string
	{
		if ($this->status === Status::NOT_FOUND)
		{
			$this->status->responseCode();
			return $this->status->value;
		}
		if (array_key_exists('template', $this->data))
		{
			echo $this->data['template']->display();
			unset($this->data['template']);
		}
		$this->status->responseCode();
		if (!empty($this->data))
		{
			return self::toJson($this->data);
		}
		return '';
	}

	public function getDataByKey(string $key): mixed
	{
		return $this->data[$key] ?? null;
	}

	/**
	 * @throws \JsonException
	 */
	private static function toJson(mixed $data): string
	{
		return json_encode($data, JSON_THROW_ON_ERROR);
	}
}
