<?php

namespace Up\Dto;

use Up\Entity\Entity;

class ProductCharacteristicAddingDto implements Entity
{
	public readonly string $title;
	public readonly string $value;

	public function __construct(string $title, string $value)
	{
		$this->title = $title;
		$this->value = $value;
	}
}