<?php

namespace Up\Repository\Order;

use Up\Entity;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\QueryResult;

class OrderRepositoryImpl implements OrderRepository
{

	public static function getAll(): array
	{
		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				inner join up_status us on up_order.status_id = us.id";

		$result = QueryResult::getQueryResult($sql);

		return self::createOrderList($result);
	}

	public static function getById(int $id): Entity\Order
	{
		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				inner join up_status us on up_order.status_id = us.id
				where up_order.id = {$id}";

		$result = QueryResult::getQueryResult($sql);

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$id = $row['id'];
				$products = [ProductRepositoryImpl::getById($row['item_id'])];
				$user = UserRepositoryImpl::getById($row['user_id']);
				$deliveryAddress = $row['delivery_address'];
				$createdAt = $row['created_at'];
				$status = $row['status'];

				$isFirstLine = false;
			}
			else
			{
				$products[] = ProductRepositoryImpl::getById($row['item_id']);
			}
		}

		return new Entity\Order(
			$id, $products, $user, $deliveryAddress, $createdAt, $status,
		);
	}

	public static function add(): void
	{
		// TODO: Implement add() method.
	}

	private static function createOrderList(\mysqli_result $result): array
	{
		$orders = [];
		!$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($orders[$row['id']]))
			{
				if (!$isFirstLine)
				{
					$orders[$id] = new Entity\Order(
						$id, $products, $user, $deliveryAddress, $createdAt, $status,
					);
				}
				$id = $row['id'];
				$products = [ProductRepositoryImpl::getById($row['item_id'])];
				$user = UserRepositoryImpl::getById($row['user_id']);
				$deliveryAddress = $row['delivery_address'];
				$createdAt = $row['created_at'];
				$status = $row['status'];

				$isFirstLine = false;
			}
			else
			{
				$products[] = ProductRepositoryImpl::getById($row['item_id']);
			}
		}

		$orders[$id] = new Entity\Order(
			$id, $products, $user, $deliveryAddress, $createdAt, $status,
		);

		return $orders;
	}
}
