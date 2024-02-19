<?php

namespace Up\Dto\Order;

use Up\Entity\Entity;

class OrderAddingAdminDto implements OrderAdding
{
	public function __construct(
		public readonly array $products,
		public readonly string $deliveryAddress,
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
