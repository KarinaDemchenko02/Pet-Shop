<?php

namespace Up\Dto\Order;

class OrderGettingAdminDto implements \Up\Dto\Dto
{
	public function __construct(
		public readonly ?int $id,
		public readonly array $products,
		public readonly ?int $userId,
		public readonly string $deliveryAddress,
		public readonly string $createdAt,
		public readonly string $editedAt,
		public readonly ?string $name,
		public readonly string $surname,
		public readonly string $status,
	)
	{
	}
}
