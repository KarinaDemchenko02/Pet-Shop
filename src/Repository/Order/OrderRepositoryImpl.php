<?php

namespace Up\Repository\Order;

use Up\Dto\Order\OrderAdding;
use Up\Dto\Order\OrderChangingDto;
use Up\Entity;
use Up\Entity\Order;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\Orm;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\OrderProductTable;
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
		$orm = Orm::getInstance();
		try
		{
			$userId = $order->userId ?? 'NULL';
			$orm->begin();
			OrderTable::add(
				[
					'user_id' => $userId,
					'delivery_address' => $order->deliveryAddress,
					'status_id' => $order->statusId,
					'name' => $order->name,
					'surname' => $order->surname,
				]
			);
			$last = $orm->last();
			foreach ($order->products as $product)
			{
				OrderProductTable::add(
					[
						'order_id' => $last,
						'product_id' => $product->info->id,
						'quantities' => $product->getQuantity(),
						'price' => $product->info->price,
					]
				);
			}
			$orm->commit();
		}
		catch (\Throwable $e)
		{
			$orm->rollback();
			throw new OrderNotCompleted();
		}
	}

	/**
	 * @throws OrderNotChanged
	 */
	public static function change(OrderChangingDto $order): void
	{
		$orm = Orm::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		OrderTable::update(
			[
				'edited_at' => $now,
				'delivery_address' => $order->deliveryAddress,
				'name' => $order->name,
				'surname' => $order->surname,
			], ['AND', ['=id' => $order->id]]
		);
		if ($orm->affectedRows() === 0)
		{
			throw new OrderNotChanged();
		}
	}

	public static function createOrderEntity(array $row): Order
	{
		return new Order(
			$row['order_id'],
			[],
			UserRepositoryImpl::createUserEntity($row),
			$row['delivery_address'],
			$row['created_at'],
			$row['edited_at'],
			$row['status_title'],
			$row['name'],
			$row['surname']
		);
	}

	private static function getOrderList($where = []): \mysqli_result|bool
	{
		return OrderTable::getList(
						[
							'order_id' => 'id',
							'delivery_address',
							'created_at',
							'edited_at',
							'name',
							'surname',
							'user' => ['user_id' => 'id'],
							'status' => ['status_title' => 'title'],
							'order_product' => ['product' => ['id'], 'quantities', 'price'],
						],
			conditions: $where
		);
	}
}
