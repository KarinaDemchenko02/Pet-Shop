<?php

namespace Up\Entity;

class ShoppingSession implements Entity
{
	public readonly ?int $id;
	public readonly ?int $userId;
	private array $products;
	public readonly ?string $createdAt;
	public readonly ?string $updatedAt;

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
		$index = $this->getIndexProduct($product->id);
		if (is_null($index))
		{
			$this->products[] = new ProductQuantity($product, $quantity);
		}
		else
		{
			$this->products[$index]->setQuantity($quantity);
		}
	}

	public function deleteProduct(Product $product)
	{
		$index = $this->getIndexProduct($product->id);
		if (!is_null($index))
		{
			unset($this->products[$index]);
		}
	}

	private function getIndexProduct(int $id): int|null
	{
		foreach ($this->products as $key => $product)
		{
			if ($product->info->id === $id)
			{
				return $key;
			}
		}

		return null;
	}

}