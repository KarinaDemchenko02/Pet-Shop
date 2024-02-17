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
				left join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id
				where id = {$id};";

		$result = QueryResult::getQueryResult($sql);

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($shoppingSession))
			{
				$shoppingSession = self::createShoppingSessionEntity($row);
			}
			else
			{
				$shoppingSession->addProduct(
					ProductRepositoryImpl::getById($row['item_id']),
					$row['quantities']
				);
			}
		}

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

	public static function getByUser(int $id): ShoppingSession
	{
		$sql = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				left join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id
				where user_id = {$id};";

		$result = QueryResult::getQueryResult($sql);

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($shoppingSession))
			{
				$shoppingSession = self::createShoppingSessionEntity($row);
			}
			else
			{
				$shoppingSession->addProduct(
					ProductRepositoryImpl::getById($row['item_id']),
					$row['quantities']
				);
			}
		}

		if (!isset($shoppingSession))
		{
			self::add($id, []);

			return self::getByUser($id);
		}

		return $shoppingSession;
	}

	private static function createShoppingSessionList(\mysqli_result $result): array
	{
		$shoppingSessions = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($shoppingSessions[$row['id']]))
			{
				$shoppingSessions[$row['id']] = self::createShoppingSessionEntity($row);
			}
			else
			{
				$shoppingSessions[$row['id']]->addProduct(
					ProductRepositoryImpl::getById($row['item_id']),
					$row['quantities']
				);
			}
		}

		return $shoppingSessions;
	}

	private static function createShoppingSessionEntity(array $row): ShoppingSession
	{
		return new ShoppingSession(
			$row['id'],
			$row['user_id'],
			[new ProductQuantity(ProductRepositoryImpl::getById($row['item_id']), $row['quantities'])],
			$row['created_at'],
			$row['updated_at']
		);
	}

	public static function add($userId, array $productsQuantities): void
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
				$addLinkToTagSQL = "INSERT IGNORE INTO up_shopping_session_item (item_id, shopping_session_id, quantities)
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

	private static function getProductIds(array $products): array
	{
		$productIds = [];
		foreach ($products as $product)
		{
			$productIds[] = $product->info->id;
		}

		return $productIds;
	}

}
