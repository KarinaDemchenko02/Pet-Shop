<?php

namespace Up\Dto\Order;

class OrderUserDto implements \Up\Dto\Dto
{
	public function __construct(
		public readonly int $id,
		public readonly string $name,
		public readonly string $path,
		public readonly string $price,
		public readonly int $quantities,
		public readonly string $status,
	)
	{
	}
}