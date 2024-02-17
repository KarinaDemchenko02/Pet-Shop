<?php

namespace Up\Entity;

class Order implements Entity
{
	public readonly int $id;
	private array $products;
	public readonly ?User $user;
	public readonly string $deliveryAddress;
	public readonly int $createdAt;
	public readonly string $status;
	public readonly ?string $name;
	public readonly ?string $surname;

	public function __construct(
		int    $id,
		array  $products,
		?User  $user,
		string $deliveryAddress,
		string $createdAt,
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
		$this->status = $status;
		$this->name = $name;
		$this->surname = $surname;
	}

	public function addProduct(Product $product)
	{
		$this->products[$product->id]=$product;
	}

	public function getProducts(): array
	{
		return $this->products;
	}

	public function addProduct(Product $product)
	{
		if (!in_array($product, $this->products, true))
		{
			$this->products[] = $product;
		}
	}

	public function getProducts(): array
	{
		return $this->products;
	}
}
