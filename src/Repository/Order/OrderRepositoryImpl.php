<?php

namespace Up\Repository\Order;

use Up\Dto\Order\OrderAdding;
use Up\Dto\Order\OrderChangingDto;
use Up\Dto\Order\OrderUserDto;
use Up\Entity;
use Up\Entity\Order;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\Orm;
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

	public static function getAllForAdmin(int $page = 1): array
	{
		$query = Query::getInstance();
		$sql = "SELECT ui.id, ui.name, img.path, ui.price, uoi.quantities, us.title FROM `up_order` uo
				JOIN up_status us ON us.id = uo.status_id 
				JOIN up_order_item uoi ON uoi.order_id = uo.id
				JOIN up_item ui ON ui.id = uoi.item_id
                JOIN up_image img ON img.item_id = ui.id
				WHERE uo.user_id = {$id}";
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$result = OrderTable::getList(['id'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}

		return self::createOrderList(self::getOrderList(['AND', ['in=id' => $ids]]));
	}

	public static function getByUser(int $userId): array
	{
		$result = OrderTable::getList(
			[
				'user_id',
				'order_product' => [
					'quantities',
					'price',
					'product' => ['id', 'name', 'image' => ['path']],
				],
				'status' => ['title'],
			], ['AND', ['=user_id' => $userId]]
		);

		$ordersUser = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$ordersUser[] = new OrderUserDto(
				$row['id'], $row['name'], $row['path'], $row['price'], $row['quantities'], $row['title'],
			);
		}
		return $ordersUser;
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
		catch (\Throwable)
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
				'status_id' => $order->statusId,
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
			$row['delivery_address'] ?? null,
			$row['created_at'] ?? null,
			$row['edited_at'] ?? null,
			$row['status_title'],
			$row['name'] ?? null,
			$row['surname'] ?? null,
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

	private static function getIds(\mysqli_result $result): array
	{
		$ids = [];
		while ($row = $result->fetch_assoc())
		{
			$ids[] = $row['id'];
		}

		return $ids;
	}
}
