<?php

namespace Up\Entity;

use Up\Entity\Entity;

class ProductCharacteristic implements Entity
{
	public readonly string $title;
	public readonly string $value;

	public function __construct(string $title, string $value)
	{
		$this->title = $title;
		$this->value = $value;
	}
}