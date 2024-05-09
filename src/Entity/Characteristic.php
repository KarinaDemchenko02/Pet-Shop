<?php

namespace Up\Entity;

use Up\Entity\Entity;

class Characteristic implements Entity
{
	public function __construct(
		readonly string $id,
		readonly string $title
	)
	{
	}
}