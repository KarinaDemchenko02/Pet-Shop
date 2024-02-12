<?php

namespace Up\Dto;

use Up\Entity\Entity;

class OrderAddingDto implements Dto
{

	public function __construct(
		public readonly ?int $userId,
		public readonly string $name,
		public readonly string $surname,
		public readonly string $deliveryAddress,
		public readonly int $productId,
		public readonly int $statusId = 2
	)
	{
	}

	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
