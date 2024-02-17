<?php

namespace Up\Repository\Order;

use Up\Dto\OrderAddingDto;
use Up\Entity;
use Up\Exceptions\Service\OrderService\OrderNotCompleted;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\Query;

class OrderRepositoryImpl implements OrderRepository
{
	private const SELECT_SQL = "select up_order.id, item_id, user_id, delivery_address, created_at , title as status, name, surname
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				left join up_status us on up_order.status_id = us.id";
	public static function getAll(): array
	{
		$query = Query::getInstance();

		$result = $query->getQueryResult(self::SELECT_SQL);

		return self::createOrderList($result);
	}

	public static function getById(int $id): Entity\Order
	{
		$query = Query::getInstance();
		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				left join up_status us on up_order.status_id = us.id
				where up_order.id = {$id}";
		$result = $query->getQueryResult($sql);

		return self::createOrderList($result)[$id];
	}

	private static function createOrderList(\mysqli_result $result): array
	{
		$orders = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($orders[$row['id']]))
			{
				$user = $row['user_id'] !== null ? UserRepositoryImpl::getById($row['user_id']) : null;
				$orders[$row['id']] = new Entity\Order(
					$row['id'],
					[],
					$user,
					$row['delivery_address'],
					$row['created_at'],
					$row['status'],
					$row['name'],
					$row['surname']
				);
			}
			if (!is_null($row['item_id']))
			{
				$orders[$row['id']]->addProduct(ProductRepositoryImpl::getById($row['item_id']));
			}
		}

		return $orders;
	}

	/**
	 * @throws OrderNotCompleted
	 */
	public static function add(OrderAddingDto $order): void
	{
		$query = Query::getInstance();
		try
		{
			$userId = $order->userId ?? 'null';
			$query->begin();

			$addNewShoppingSessionSQL = "INSERT INTO up_order (up_order.user_id, up_order.delivery_address, up_order.status_id, up_order.created_at, up_order.name, up_order.surname) 
				VALUES ($userId, '{$order->deliveryAddress}', {$order->statusId}, CURRENT_TIMESTAMP, '{$order->name}', '{$order->surname}')";

			$query->getQueryResult($addNewShoppingSessionSQL);
			$last = $query->last();
			$price = ProductRepositoryImpl::getById($order->productId)->price;

			$addLinkToItemSQL = "INSERT INTO up_order_item (order_id, item_id, quantities, price)
									VALUES ({$last}, {$order->productId}, 1, {$price})";

			$query->getQueryResult($addLinkToItemSQL);
			$query->commit();
		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new OrderNotCompleted();
		}
	}

	public static function getColumn(): array
	{
		$query = Query::getInstance();
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'up_order';";

		$result = $query->getQueryResult($sql);
		$columns = [];
		while ($column = mysqli_fetch_column($result))
		{
			$columns[] = $column;
		}

		return $columns;
	}
}
