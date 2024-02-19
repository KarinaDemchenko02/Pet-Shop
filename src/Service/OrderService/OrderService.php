<?php

namespace Up\Service\OrderService;

use Up\Dto\Order\OrderAdding;
use Up\Dto\Order\OrderAddingAdminDto;
use Up\Dto\Order\OrderGettingAdminDto;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Repository\Order\OrderRepositoryImpl;

class OrderService
{

	/**
	 * @throws OrderNotCompleted
	 */
	public static function createOrder(OrderAdding $dto): void
	{
		OrderRepositoryImpl::add($dto);
	}

	public static function getAllOrder(): array
	{
		$orders = OrderRepositoryImpl::getAll();

		$ordersDto = [];
		foreach ($orders as $order)
		{
			$ordersDto[] = new OrderGettingAdminDto(
				$order->id,
				$order->getProducts(),
				is_null($order->user) ? null : $order->user->id,
				$order->deliveryAddress,
				$order->createdAt,
				$order->editedAt,
				$order->name,
				$order->surname,
				(int)$order->status,
			);

		}

		return $ordersDto;
	}

	public static function gelColumn(): array
	{
		return OrderRepositoryImpl::getColumn();
	}

	/**
	 * @throws OrderNotDeleted
	 */
	public static function deleteOrder(int $id): void
	{
		OrderRepositoryImpl::delete($id);
	}
}
