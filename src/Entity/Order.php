<?php

namespace Up\Entity;

class Order implements Entity
{
	public readonly int $id;
	public readonly array $products;
	public readonly User $user;
	public readonly string $deliveryAddress;
	public readonly int $createdAt;
	public readonly string $status;

	public function __construct(
		int    $id,
		array  $products,
		User   $user,
		string $deliveryAddress,
		string $createdAt,
		string $status
	)
	{
		$this->id = $id;
		$this->products = $products;
		$this->user = $user;
		$this->deliveryAddress = $deliveryAddress;
		$this->createdAt = strtotime($createdAt);
		$this->status = $status;
	}

}
