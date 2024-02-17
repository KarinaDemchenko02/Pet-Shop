<?php

namespace Up\Repository\Order;

use Up\Dto\OrderAddingDto;
use Up\Entity;
use Up\Exceptions\Service\OrderService\OrderNotCompleted;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\QueryResult;

class OrderRepositoryImpl implements OrderRepository
{

	public static function getAll(): array
	{
		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at , title as status, name, surname
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

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($order))
			{
				$order = self::createOrderEntity($row);
			}
			else
			{
				$order->addProduct(ProductRepositoryImpl::getById($row['item_id']));
			}
		}

		return $order;
	}

	private static function createOrderList(\mysqli_result $result): array
	{
		$orders = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($orders[$row['id']]))
			{
				$orders[$row['id']] = self::createOrderEntity($row);
			}
			else
			{
				$orders[$row['id']]->addProduct(ProductRepositoryImpl::getById($row['item_id']));
			}
		}

		return $orders;
	}

	private static function createOrderEntity(array $row): Entity\Order
	{
		return new Entity\Order(
			$row['id'],
			[ProductRepositoryImpl::getById($row['item_id'])],
			$row['user_id'] !== null ? UserRepositoryImpl::getById($row['user_id']) : null,
			$row['delivery_address'],
			$row['created_at'],
			$row['status'],
		);
	}

	/**
	 * @throws OrderNotCompleted
	 */
	public static function add(OrderAddingDto $order): void
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		try
		{
			$userId = $order->userId ?? 'null';

			mysqli_begin_transaction($connection);
			$addNewShoppingSessionSQL = "INSERT INTO up_order (up_order.user_id, up_order.delivery_address, up_order.status_id, up_order.created_at, up_order.name, up_order.surname) 
				VALUES ($userId, '{$order->deliveryAddress}', {$order->statusId}, CURRENT_TIMESTAMP, '{$order->name}', '{$order->surname}')";
			QueryResult::getQueryResult($addNewShoppingSessionSQL);
			$last = mysqli_insert_id($connection);
			$price = ProductRepositoryImpl::getById($order->productId)->price;
			$addLinkToItemSQL = "INSERT INTO up_order_item (order_id, item_id, quantities, price)
									VALUES ({$last}, {$order->productId}, 1, {$price})";
			QueryResult::getQueryResult($addLinkToItemSQL);
			mysqli_commit($connection);
		}
		catch (\Throwable)
		{
			mysqli_rollback($connection);
			throw new OrderNotCompleted();
		}
	}

	public static function getColumn(): array
	{
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'up_order';";

		$result = QueryResult::getQueryResult($sql);

		$columns = [];

		while ($column = mysqli_fetch_column($result))
		{
			$columns[] = $column;
		}

		return $columns;
	}
}
