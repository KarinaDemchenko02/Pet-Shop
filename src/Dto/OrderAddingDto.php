<?php

namespace Up\Dto;

use Up\Dto\Dto;
use Up\Entity\Entity;

class OrderAddingDto implements Dto
{
	public readonly int $userId;
	public readonly string $deliveryAddress;
	public readonly int $statusId;
	public readonly int $createdAt;
	public readonly array $products;


	public function __construct(int $userId, string $deliveryAddress, int $statusId, int $createdAt, array $products)
	{
		$this->userId = $userId;
		$this->deliveryAddress = $deliveryAddress;
		$this->statusId = $statusId;
		$this->createdAt = $createdAt;
		$this->products = $products;
	}

	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}