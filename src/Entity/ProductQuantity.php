<?php

namespace Up\Entity;

use Up\Entity\Entity;

class ProductQuantity implements Entity
{

	public readonly Product $info;
	private int $quantity;
	private float $price;

	public function __construct(Product $info, $quantity, $price = null)
	{
		$this->info = $info;
		$this->quantity = $quantity;
		$this->price = is_null($price) ? $info->price : $price;
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