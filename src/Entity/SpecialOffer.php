<?php

namespace Up\Entity;

use Up\Entity\Entity;

class SpecialOffer implements Entity
{
	public function __construct(
		readonly string $id,
		readonly string $title,
		readonly string $description,
	)
	{
	}
}