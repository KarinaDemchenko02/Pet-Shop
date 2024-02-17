<?php

namespace Up\Entity;

class ShoppingSession implements Entity
{
	readonly ?int $id;
	readonly ?int $userId;
	private array $products;
	readonly ?string $createdAt;
	readonly ?string $updatedAt;

	public function __construct(?int $id, ?int $userId, array $products, ?string $createdAt, ?string $updatedAt)
	{
		$this->id = $id;
		$this->userId = $userId;
		$this->products = $products;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
	}

	public function getProducts(): array
	{
		return $this->products;
	}

	public function addProduct(Product $product, int $quantity): void
	{
		$this->products[$product->id] = new ProductQuantity($product, $quantity);
	}

	public function deleteProduct(Product $product)
	{
		if (isset($this->products[$product->id]))
		{
			unset($this->products[$product->id]);
		}
	}

}

/*
$shoppingSession->id
$shoppingSession->user : User

$shoppingSession->products[i]->info : Product
$shoppingSession->products[i]->quantity : int
 */
