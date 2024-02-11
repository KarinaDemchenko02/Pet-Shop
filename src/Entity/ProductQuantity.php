<?php

namespace Up\Entity;

use Up\Entity\Entity;

class ProductQuantity implements Entity
{

	readonly Product $info;
	private int $quantity;
	private int $price;


	public function __construct(Product $info)
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

	public function getPrice(): int
	{
		return $this->price;
	}

	public function setPrice(int $price): void
	{
		$this->price = $price;
	}

}