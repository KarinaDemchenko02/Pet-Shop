<?php

namespace Up\Entity;

class ShoppingSession implements Entity
{
	readonly int $id;
	readonly User $user;
	readonly array $products;
	readonly int $createdAt;
	readonly int $updatedAt;

	public function __construct(int $id, User $user, array $products, int $createdAt, int $updatedAt)
	{
		$this->id = $id;
		$this->user = $user;
		$this->products = $products;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
	}

}

/*
$shoppingSession->id
$shoppingSession->user : User

$shoppingSession->products[i]->info : Product
$shoppingSession->products[i]->quantity : int
 */