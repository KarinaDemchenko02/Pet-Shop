<?php

namespace Up\Service\OrderService;

use Up\Dto\OrderAddingDto;
use Up\Exceptions\Service\OrderService\OrderNotCompleted;
use Up\Repository\Order\OrderRepositoryImpl;

class OrderService
{

	/**
	 * @throws OrderNotCompleted
	 */
	public static function buyProduct(OrderAddingDto $dto): void
	{
		OrderRepositoryImpl::add($dto);
	}
}
