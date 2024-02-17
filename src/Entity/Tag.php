<?php

namespace Up\Entity;

class Tag implements Entity
{
	public function __construct(
		readonly string $id,
		readonly string $title
	)
	{
	}
}
