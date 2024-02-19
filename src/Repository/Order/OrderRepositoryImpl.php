<?php

namespace Up\Repository\Order;

use Up\Dto\Order\OrderAdding;
use Up\Dto\Order\OrderAddingDto;
use Up\Dto\Product\ProductAddingDto;
use Up\Entity;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\Query;

class OrderRepositoryImpl implements OrderRepository
{
	private const SELECT_SQL = "select up_order.id, item_id, user_id, delivery_address, created_at, edited_at , title as status, name, surname, quantities
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				left join up_status us on up_order.status_id = us.id ";

	public static function getAll(): array
	{
		$query = Query::getInstance();

		$result = $query->getQueryResult(self::SELECT_SQL);

		return self::createOrderList($result);
	}

	public static function getById(int $id): Entity\Order
	{
		$query = Query::getInstance();
		$sql = self::SELECT_SQL . "where up_order.id = {$id}";
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
				$query = Query::getInstance();
				$sql = "SELECT item_id, quantities, price
				FROM up_order_item oi
				WHERE oi.order_id = {$row['id']}";
				$productResult = $query->getQueryResult($sql);
				$productDto = [];
				while ($productRow = mysqli_fetch_assoc($productResult))
				{
					$productDto[] = new ProductAddingDto(
						$productRow['item_id'],
						$productRow['quantities'],
						$productRow['price'],
					);
				}

				$user = $row['user_id'] !== null ? UserRepositoryImpl::getById($row['user_id']) : null;
				$orders[$row['id']] = new Entity\Order(
					$row['id'],
					$productDto,
					$user,
					$row['delivery_address'],
					$row['created_at'],
					$row['edited_at'],
					$row['status'],
					$row['name'],
					$row['surname']
				);
			}
		}

		return $orders;
	}

	/**
	 * @throws OrderNotCompleted
	 */
	public static function add(OrderAdding $order): void
	{
		$query = Query::getInstance();
		try
		{
			$userId = $order->userId ?? 'null';
			$query->begin();

			$addNewShoppingSessionSQL = "INSERT INTO up_order (up_order.user_id, up_order.delivery_address, up_order.status_id, up_order.name, up_order.surname) 
				VALUES ($userId, '{$order->deliveryAddress}', {$order->statusId}, '{$order->name}', '{$order->surname}')";

			$query->getQueryResult($addNewShoppingSessionSQL);
			$last = $query->last();
			foreach ($order->products as $product)
			{
				$addLinkToItemSQL = "INSERT INTO up_order_item (order_id, item_id, quantities, price)
									VALUES ({$last}, {$product->info->id},
											{$product->getQuantity()}, {$product->info->price})";
				$query->getQueryResult($addLinkToItemSQL);
			}
			$query->commit();
		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new OrderNotCompleted();
		}
	}

	/**
	 * @throws OrderNotDeleted
	 */
	public static function delete($id)
	{
		$query = Query::getInstance();
		try
		{
			$query->begin();
			$deleteLinkOrderSQL = "DELETE FROM up_order_item WHERE order_id=$id";
			$query->getQueryResult($deleteLinkOrderSQL);
			$deleteOrderSQL = "DELETE FROM up_order WHERE id=$id";
			$query->getQueryResult($deleteOrderSQL);
			if (Query::affectedRows() === 0)
			{
				throw new OrderNotDeleted();
			}
			$query->commit();
		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new OrderNotDeleted();
		}
	}

	/**
	 * @throws OrderNotChanged
	 */
	public static function change(Entity\Order $order)
	{
		$query = Query::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		$itemIds = implode(", ", self::getItemsIds($order->getProducts()));
		try
		{
			$query->begin();
			$changeOrderSQL = "UPDATE up_order SET edited_at='{$now}' WHERE id={$order->id}";
			$query->getQueryResult($changeOrderSQL);
			$deleteItemLinkSQL = "DELETE FROM up_order_item WHERE item_id NOT IN ($itemIds)";
			$query->getQueryResult($deleteItemLinkSQL);
			foreach ($order->getProducts() as $item)
			{
				$addLinkToItemSQL = "INSERT IGNORE INTO up_order_item (order_id, item_id, quantities, price)
									VALUES ({$order->id}, {$item->info->id}, {$item->getQuantity()}, {$item->info->price})";
				$query->getQueryResult($addLinkToItemSQL);
			}
			if (Query::affectedRows() === 0)
			{
				throw new OrderNotChanged();
			}
			$query->commit();

		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new OrderNotChanged();
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
			if ($column === 'status_id')
			{
				$column = 'status';
			}
			$columns[] = $column;
		}

		return $columns;
	}

	private static function getItemsIds(array $items)
	{
		$itemIds = [];
		foreach ($items as $item)
		{
			$itemIds[] = $item->info->id;
		}

		return $itemIds;
	}
}
