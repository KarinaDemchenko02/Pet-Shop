<?php

namespace Up\Entity;

class Image implements Entity
{
	readonly int $id;
	readonly string $path;
	readonly int $itemId;
	readonly string $type;

	public function __construct(int $id, string $path, int $itemId, string $type)
	{
		$this->id = $id;
		$this->path = $path;
		$this->itemId = $itemId;
		$this->type = $type;
	}
}