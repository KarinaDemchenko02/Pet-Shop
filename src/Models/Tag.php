<?php

namespace Up\Models;

class Tag
{
	readonly int $id;
	readonly string $title;

	public function __construct(
		string $id,
		string $title
	)
	{
		$this->id = $id;
		$this->title = $title;
	}
}