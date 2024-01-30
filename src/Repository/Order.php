<?php

namespace Up\Repository;

use Up\Repository\Repository;
use Up\Service\Database;
use Up\Models;

class Order extends Repository
{

	public static function getAll(): array
	{
		$database = new Database();
		$connection = $database->getDbConnection();

		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				inner join up_status us on up_order.status_id = us.id";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$orders = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($orders[$row['id']]))
			{
				$user = User::getById($row['user_id']);
				$product = Product::getById($row['item_id']);

				$orders[$row['id']] = new Models\Order(
					$row['id'],
					$product,
					$user,
					$row['delivery_address'],
					$row['created_at'],
					$row['status']
				);
			}
			else
			{
				$product = Product::getById($row['item_id']);
				$orders[$row['id']]->addProduct($product);
			}

		}

		return $orders;

	}

	public static function getById(int $id): Models\Order
	{
		$database = new Database();
		$connection = $database->getDbConnection();

		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				inner join up_status us on up_order.status_id = us.id
				where up_order.id = {$id}";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($order))
			{
				$user = User::getById($row['user_id']);
				$product = Product::getById($row['item_id']);

				$order = new Models\Order(
					$row['id'],
					$product,
					$user,
					$row['delivery_address'],
					$row['created_at'],
					$row['status']
				);
			}
			else
			{
				$product = Product::getById($row['item_id']);
				$order->addProduct($product);
			}
		}

		return $order;

	}
}