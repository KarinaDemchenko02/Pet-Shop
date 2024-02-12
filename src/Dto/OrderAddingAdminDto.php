<?php

namespace Up\Dto;

use Up\Entity\Entity;

class OrderAddingAdminDto implements Dto
{
	public function __construct(
		public readonly ?int $id,
		public readonly int $productId,
		public readonly ?int $userId,
		public readonly string $deliveryAddress,
		public readonly string $createdAt,
		public readonly string $name,
		public readonly string $surname,


		public readonly int $statusId = 2
	)
	{
	}
	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}