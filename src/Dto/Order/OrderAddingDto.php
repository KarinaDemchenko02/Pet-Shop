<?php

namespace Up\Dto\Order;

use Up\Entity\Entity;
use Up\Entity\ShoppingSession;

class OrderAddingDto implements OrderAdding
{
	public readonly ?int $userId;
	public readonly ?string $name;
	public readonly string $surname;
	public readonly string $deliveryAddress;
	public readonly array $products;
	public readonly int $statusId;

	public function __construct(ShoppingSession $ShoppingSession, $name, $surname, $deliveryAddress, $statusId = 2)
	{
		$this->userId = $ShoppingSession->userId;
		$this->name = $name;
		$this->surname = $surname;
		$this->deliveryAddress = $deliveryAddress;
		$this->statusId = $statusId;
		$this->products = $ShoppingSession->getProducts();
	}

	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
