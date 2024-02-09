<?php

namespace Up\Entity;

use Up\Entity\Entity;

class Image implements Entity
{
	readonly int $id;
	readonly string $path;
	readonly string $characteristic;

	public function __construct(int $id, string $path, string $characteristic)
	{
		$this->id = $id;
		$this->path = $path;
		$this->characteristic = $characteristic;
	}
}