<?php

namespace Up\Entity;

use Up\Entity\Entity;

class ProductQuantity implements Entity
{

	readonly Product $info;
	private int $quantity;


	public function __construct(Product $info, $quantity)
	{
		$this->info = $info;
		$this->quantity = $quantity;
	}

	public function setQuantity(int $quantity): void
	{
		$this->quantity = $quantity;
	}

	public function getQuantity(): int
	{
		return $this->quantity;
	}

	public function getPrice(): int
	{
		return $this->price;
	}

	public function setPrice(int $price): void
	{
		$this->price = $price;
	}

}