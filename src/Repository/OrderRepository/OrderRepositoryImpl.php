<?php

namespace Up\Repository\OrderRepository;


use Exception;
use Up\Entity\Order;
use Up\Entity\Product;
use Up\Repository\UserRepository\UserRepositoryImpl;


class OrderRepositoryImpl implements OrderRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Database\Connector::getInstance()->option('DB_HOST'),
			\Up\Util\Database\Connector::getInstance()->option('DB_USER'),
			\Up\Util\Database\Connector::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Database\Connector::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				inner join up_status us on up_order.status_id = us.id";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$orders = [];
		!$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				if (!$isFirstLine)
				{
					$orders[$id] = new Order(
						$id, $products, $user, $deliveryAddress, $createdAt, $status,
					);
				}
				$id = $row['id'];
				$products = [Product::getById($row['item_id'])];
				$user = UserRepositoryImpl::getById($row['user_id']);
				$deliveryAddress = $row['delivery_address'];
				$createdAt = $row['created_at'];
				$status = $row['status'];

				$isFirstLine = false;
			}
			else
			{
				$products[] = Product::getById($row['item_id']);
			}
		}

		$orders[$id] = new Order(
			$id, $products, $user, $deliveryAddress, $createdAt, $status,
		);

		return $orders;

	}

	public static function getById(int $id): Models\Order
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Database\Connector::getInstance()->option('DB_HOST'),
			\Up\Util\Database\Connector::getInstance()->option('DB_USER'),
			\Up\Util\Database\Connector::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Database\Connector::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select up_order.id, item_id, user_id, delivery_address, created_at ,title as status
				from up_order inner join up_order_item uoi on up_order.id = uoi.order_id
				inner join up_status us on up_order.status_id = us.id
				where up_order.id = {$id}";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$id = $row['id'];
				$products = [Product::getById($row['item_id'])];
				$user = UserRepositoryImpl::getById($row['user_id']);
				$deliveryAddress = $row['delivery_address'];
				$createdAt = $row['created_at'];
				$status = $row['status'];

				$isFirstLine = false;
			}
			else
			{
				$products[] = Product::getById($row['item_id']);
			}
		}
		$order = new Order(
			$id, $products, $user, $deliveryAddress, $createdAt, $status,
		);

		return $order;

	}
}
