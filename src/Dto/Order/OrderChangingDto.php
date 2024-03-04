<?php

namespace Up\Dto\Order;

class OrderChangingDto implements \Up\Dto\Dto
{
	public function __construct(
		public readonly int $id,
		public readonly string $deliveryAddress,
		public readonly string $name,
		public readonly string $surname,
		public readonly int $statusId,
	)
	{
	}
}
