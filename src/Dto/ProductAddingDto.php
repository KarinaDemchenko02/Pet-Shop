<?php

namespace Up\Dto;

use Up\Entity\Entity;

class ProductAddingDto implements Dto
{
	public function __construct(
		public readonly string $title,
		public readonly string $description,
		public readonly string $price,
		public readonly string $imagePath,
		public readonly array $tags,
	){}

	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
