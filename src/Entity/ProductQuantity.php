<?php

namespace Up\Entity;

use Up\Entity\Entity;

class ProductQuantity implements Entity
{
	readonly Product $info;
	readonly int $quantity;

	/**
	 * @param Product $info
	 * @param int $quantity
	 */
	public function __construct(Product $info, int $quantity)
	{
		$this->info = $info;
		$this->quantity = $quantity;
	}

}