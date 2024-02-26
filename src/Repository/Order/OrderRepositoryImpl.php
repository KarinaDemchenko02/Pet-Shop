<?php

namespace Up\Repository\Order;

use Up\Dto\Order\OrderAdding;
use Up\Dto\Order\OrderChangingDto;
use Up\Entity;
use Up\Entity\Order;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\OrderTable;

class OrderRepositoryImpl implements OrderRepository
{
	public static function getAll(): array
	{
		return self::createOrderList(self::getOrderList());
	}

	private static function createOrderList(\mysqli_result $result): array
	{
		$orders = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($orders[$row['order_id']]))
			{
				$orders[$row['order_id']] = self::createOrderEntity($row);
			}
			if (!is_null($row['id']))
			{
				$orders[$row['order_id']]->addProduct(
					new Entity\ProductQuantity(
						ProductRepositoryImpl::createProductEntity($row), $row['quantities'], $row['price']
					)
				);
			}
		}

		return $orders;
	}

	public static function getById(int $id): Entity\Order
	{
		return self::createOrderList(self::getOrderList(['AND', '=order_id' => $id]))[$id];
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
	public static function change(OrderChangingDto $order): void
	{
		$query = Query::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		// $itemIds = implode(", ", self::getItemsIds($order->getProducts()));
		try
		{
			$query->begin();
			$changeOrderSQL = "
				UPDATE up_order
				SET
					edited_at='{$now}',
					delivery_address='{$order->deliveryAddress}',
					name='{$order->name}',
					surname='{$order->surname}'
				WHERE id={$order->id}";
			$query->getQueryResult($changeOrderSQL);

			// $deleteItemLinkSQL = "DELETE FROM up_order_item WHERE item_id NOT IN ($itemIds)";
			// $query->getQueryResult($deleteItemLinkSQL);
			// foreach ($order->getProducts() as $item)
			// {
			// 	$addLinkToItemSQL = "INSERT IGNORE INTO up_order_item (order_id, item_id, quantities, price)
			// 						VALUES ({$order->id}, {$item->info->id}, {$item->getQuantity()}, {$item->info->price})";
			// 	$query->getQueryResult($addLinkToItemSQL);
			// }
			if (Query::affectedRows() === 0)
			{
				throw new OrderNotChanged();
			}
			$query->commit();
		}
		catch (\Throwable|OrderNotChanged)
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

	public static function createOrderEntity(array $row): Order
	{
		return new Order(
			$row['order_id'],
			[],
			UserRepositoryImpl::createUserEntity($row),
			$row['delivery_address'],
			$row['status_title'],
			$row['created_at'],
			$row['edited_at'],
			$row['name'],
			$row['surname']
		);
	}

	private static function getOrderList($where = []): \mysqli_result|bool
	{
		return OrderTable::getList(
						['order_id' => 'id', 'delivery_address', 'created_at', 'edited_at', 'name', 'surname'],
						[
							'user' => ['user_id' => 'id'],
							'status' => ['status_title' => 'title'],
							'product' => ['id', 'quantities', 'price'],
						],
			conditions: $where
		);
	}
}
