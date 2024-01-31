<?php

namespace Up\Models;

class Order
{
	readonly int $id;
	readonly array $products;
	readonly User $user;
	readonly string $deliveryAddress;
	readonly int $createdAt;
	readonly string $status;

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