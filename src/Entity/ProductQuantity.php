<?php

namespace Up\Entity;

use Up\Entity\Entity;

class ProductQuantity implements Entity
{
	readonly Product $info;

	/**
	 * @param Product $info
	 * @param int $quantity
	 */
	public function __construct(Product $info, private int $quantity)
	{
		$this->info = $info;
	}

	public function setQuantity(int $quantity): void
	{
		$this->quantity = $quantity;
	}

	public function getQuantity(): int
	{
		return $this->quantity;
	}

}