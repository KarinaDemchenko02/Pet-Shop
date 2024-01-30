<?php

namespace Up\Models;

class Order
{
	private int $id;
	private array $products;
	private User $user;
	private string $deliveryAddress;
	private int $createdAt;
	private string $status;

	public function __construct(
		int    $id,
		Product  $products,
		User   $user,
		string $deliveryAddress,
		string    $createdAt,
		string $status
	)
	{
		$this->id = $id;
		$this->products = [$products];
		$this->user = $user;
		$this->deliveryAddress = $deliveryAddress;
		$this->createdAt = strtotime($createdAt);
		$this->status = $status;
	}

	public function addProduct(Product $product)
	{
		$this->products[]=$product;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getProducts(): array
	{
		return $this->products;
	}

	public function getUser(): User
	{
		return $this->user;
	}

	public function getDeliveryAddress(): string
	{
		return $this->deliveryAddress;
	}

	public function getCreatedAt(): int
	{
		return $this->createdAt;
	}

	public function getStatus(): string
	{
		return $this->status;
	}
}