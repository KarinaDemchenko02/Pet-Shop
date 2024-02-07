<?php

namespace Up\Entity;

class ShoppingSession
{
	readonly int $id;
	readonly User $user;
	readonly array $products;

	/**
	 * @param int $id
	 * @param User $user
	 * @param array $products
	 */
	public function __construct(int $id, User $user, array $products)
	{
		$this->id = $id;
		$this->user = $user;
		$this->products = $products;
	}

}

/*
$shoppingSession->id
$shoppingSession->user : User

$shoppingSession->products[i]->info : Product
$shoppingSession->products[i]->quantity : int



 */