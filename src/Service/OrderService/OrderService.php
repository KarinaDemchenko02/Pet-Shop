<?php

namespace Up\Service\OrderService;

use Up\Dto\OrderAddingAdminDto;
use Up\Dto\OrderAddingDto;
use Up\Exceptions\Order\OrderNotCompleted;
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
		$orders = OrderRepositoryImpl::getAll();

		$ordersDto = [];
		foreach ($orders as $order)
		{
			foreach ($order->getProducts() as $product)
			{
				$ordersDto[] = new OrderAddingAdminDto(
					$order->id,
					$product->info->id,
					is_null($order->user) ? null : $order->user->id,
					$order->deliveryAddress,
					$order->createdAt,
					$order->name,
					$order->surname,
					(int)$order->status,
				);
			}
		}

		return $ordersDto;
	}

	public static function gelColumn()
	{
		return OrderRepositoryImpl::getColumn();
	}
}
