<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ProductQuantity;
use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\QueryResult;

class ShoppingSessionRepositoryImpl implements ShoppingSessionRepository
{

	public static function getById(int $id): ShoppingSession
	{
		$sql = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				inner join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id
				where id = {$id};";

		$result = QueryResult::getQueryResult($sql);

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$user = UserRepositoryImpl::getById($row['user_id']);
				$createdAt = $row['created_at'];
				$updatedAt = $row['updated_at'];
				$isFirstLine = false;
			}

			$products[$row['item_id']] = new ProductQuantity(
				ProductRepositoryImpl::getById($row['item_id']), $row['quantities']
			);

		}
		$shoppingSession = new ShoppingSession(
			$id, $user, $products, $createdAt, $updatedAt
		);

		return $shoppingSession;
	}

	public static function getAll(): array
	{
		$sql = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				inner join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id";

		$result = QueryResult::getQueryResult($sql);

		return self::createShoppingSessionList($result);
	}

	private static function createShoppingSessionList(\mysqli_result $result): array
	{
		$ShoppingSessions = [];
		!$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($ShoppingSessions[$row['id']]))
			{
				$products = [];

				if (!$isFirstLine)
				{
					$ShoppingSessions[$id] = new ShoppingSession(
						$id, $user, $products, $createdAt, $updatedAt
					);
				}
				$id = $row['id'];
				$products[$row['item_id']] = new ProductQuantity(
					ProductRepositoryImpl::getById($row['item_id']), $row['quantities']
				);

				$user = UserRepositoryImpl::getById($row['user_id']);
				$createdAt = $row['created_at'];
				$updatedAt = $row['updated_at'];

				$isFirstLine = false;
			}
			else
			{
				$products[$row['item_id']] = new ProductQuantity(
					ProductRepositoryImpl::getById($row['item_id']), $row['quantities']
				);
			}
		}

		$ShoppingSessions[$id] = new ShoppingSession(
			$id, $user, $products, $createdAt, $updatedAt
		);

		return $ShoppingSessions;
	}

	public static function add($userId, array $productsQuantities)
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		try
		{
			mysqli_begin_transaction($connection);
			$addNewShoppingSessionSQL = "INSERT INTO up_shopping_session (user_id) 
				VALUES ({$userId})";
			QueryResult::getQueryResult($addNewShoppingSessionSQL);
			$last = mysqli_insert_id($connection);
			foreach ($productsQuantities as $product)
			{
				$addLinkToItemSQL = "INSERT INTO up_shopping_session_item (item_id, shopping_session_id, quantities)
									VALUES ({$product->info->id}, {$last}, {$product->getQuantity()})";
				QueryResult::getQueryResult($addLinkToItemSQL);
			}
			mysqli_commit($connection);
		}
		catch (\Throwable $e)
		{
			mysqli_rollback($connection);
			throw $e;
		}
	}

	public static function change(ShoppingSession $shoppingSession): void
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		$strProduct = implode(', ', self::getProductIds($shoppingSession->getProducts()));
		try
		{
			mysqli_begin_transaction($connection);
			$changeProductSQL = "UPDATE up_shopping_session SET updated_at='{$now}' where id = {$shoppingSession->id}";
			QueryResult::getQueryResult($changeProductSQL);
			$deleteProductSQL = "DELETE FROM up_shopping_session_item WHERE shopping_session_id={$shoppingSession->id} AND item_id NOT IN ({$strProduct})";
			QueryResult::getQueryResult($deleteProductSQL);
			foreach ($shoppingSession->getProducts() as $item)
			{
				$addLinkToTagSQL =
					"INSERT IGNORE INTO up_shopping_session_item (item_id, shopping_session_id, quantities)
					VALUES ({$item->info->id}, {$shoppingSession->id}, {$item->getQuantity()})";
				QueryResult::getQueryResult($addLinkToTagSQL);
			}
			mysqli_commit($connection);
		}
		catch (\Throwable $e)
		{
			mysqli_rollback($connection);
			throw $e;
		}
	}

	private static function getProductIds(array $products)
	{
		$productIds = [];
		foreach ($products as $product)
		{
			$productIds[] = $product->info->id;
		}

		return $productIds;
	}

}