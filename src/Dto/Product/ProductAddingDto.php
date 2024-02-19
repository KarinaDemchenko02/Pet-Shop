<?php

namespace Up\Dto\Product;

class ProductAddingDto implements \Up\Dto\Dto
{
	public function __construct(
		public readonly int $id,
		public readonly int $quantity,
		public readonly float $price,
	)
	{
	}
}
