<?php

namespace Up\Service\OrderService;

use Up\Dto\Order\OrderAdding;
use Up\Dto\Order\OrderAddingAdminDto;
use Up\Dto\Order\OrderAddingDto;
use Up\Dto\Order\OrderChangingDto;
use Up\Dto\Order\OrderGettingAdminDto;
use Up\Entity\Order;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Repository\Order\OrderRepositoryImpl;
use Up\Util\Database\Tables\OrderTable;

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
				(string)$order->status,
			);

		}
		return $ordersDto;
	}

	public static function gelColumn(): array
	{
		return OrderTable::getColumnsName();
	}

	/**
	 * @throws OrderNotDeleted
	 */
	public static function disableOrder(int $id): void
	{
		OrderRepositoryImpl::disable($id);
	}

	/**
	 * @throws OrderNotChanged
	 */
	public static function changeOrder(OrderChangingDto $dto): void
	{
		OrderRepositoryImpl::change($dto);
	}

	public static function getOrderByUser(int $id): array
	{
		return OrderRepositoryImpl::getByUser($id);
	}

	public static function getAllProductsForAdmin(int $page = 1): array
	{
		$orders = OrderRepositoryImpl::getAllForAdmin($page);
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
				(string)$order->status,
			);
		}

		return $ordersDto;
	}
}
