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

	public static function getAllOrder(): array
	{
		$orders =  OrderRepositoryImpl::getAll();

		$ordersDto = [];
		foreach ($orders as $order)
		{
			$ordersDto[] = new OrderAddingDto(
				$order->user->id,
				$order->user->name,
				'surname',
				$order->deliveryAddress,
				$order->products[0]->id,
				(int) $order->status,
				$order->createdAt
			);
		}

		return $ordersDto;
	}

	public static function gelColumn()
	{
		return OrderRepositoryImpl::getColumn();
	}
}
