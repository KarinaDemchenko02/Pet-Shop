<?php

namespace Up\Entity;

class Order implements Entity
{
	public readonly int $id;
	private array $products;
	public readonly ?User $user;
	public readonly ?string $deliveryAddress;
	public readonly ?int $createdAt;
	public readonly ?int $editedAt;
	public readonly ?string $status;
	public readonly ?string $name;
	public readonly ?string $surname;

	public function __construct(
		int    $id,
		array  $products,
		?User  $user,
		string $deliveryAddress,
		string $createdAt,
		string $editedAt,
		string $status,
		?string $name,
		?string $surname
	)
	{
		$this->id = $id;
		$this->products = $products;
		$this->user = $user;
		$this->deliveryAddress = $deliveryAddress;
		$this->createdAt = strtotime($createdAt);
		$this->editedAt = strtotime($editedAt);
		$this->status = $status;
		$this->name = $name;
		$this->surname = $surname;
	}

	public function addProduct(ProductQuantity $product)
	{
		$this->products[$product->info->id] = $product;
	}

	public function getProducts(): array
	{
		return $this->products;
	}
}
