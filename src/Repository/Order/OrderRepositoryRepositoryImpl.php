<?php

namespace Up\Repository\Order;

use Up\Models;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;

class OrderRepositoryRepositoryImpl implements OrderRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Service\Database::getInstance(
			\Up\Service\Configuration::getInstance()->option('DB_HOST'),
			\Up\Service\Configuration::getInstance()->option('DB_USER'),
			\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Service\Configuration::getInstance()->option('DB_NAME')
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
					$orders[$id] = new Models\Order(
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

		$orders[$id] = new Models\Order(
			$id, $products, $user, $deliveryAddress, $createdAt, $status,
		);

		return $orders;

	}

	public static function getById(int $id): Models\Order
	{
		$connection = \Up\Service\Database::getInstance(
			\Up\Service\Configuration::getInstance()->option('DB_HOST'),
			\Up\Service\Configuration::getInstance()->option('DB_USER'),
			\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Service\Configuration::getInstance()->option('DB_NAME')
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
		$order = new Models\Order(
			$id, $products, $user, $deliveryAddress, $createdAt, $status,
		);

		return $order;

	}
}