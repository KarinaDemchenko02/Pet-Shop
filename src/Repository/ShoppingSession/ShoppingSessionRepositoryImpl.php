<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Database\Query;

class ShoppingSessionRepositoryImpl implements ShoppingSessionRepository
{
	private const SELECT_SQL = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				left join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id ";

	public static function getById(int $id): ShoppingSession
	{
		$query = Query::getInstance();
		$sql = self::SELECT_SQL . "where id = {$id}";

		$result = $query->getQueryResult($sql);

		return self::createShoppingSessionList($result);
	}

	public static function getAll(): array
	{
		$query = Query::getInstance();

		$result = $query->getQueryResult(self::SELECT_SQL);

		return self::createShoppingSessionList($result);
	}

	public static function getByUser($id)
	{
		$query = Query::getInstance();
		$sql = self::SELECT_SQL . "where user_id = {$id};";

		$result = $query->getQueryResult($sql);

		return self::createShoppingSessionList($result);
	}

	private static function createShoppingSessionList(\mysqli_result $result): array|ShoppingSession
	{
		$shoppingSessions = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($shoppingSessions[$row['id']]))
			{
				$id = $row['id'];
				$shoppingSessions[$id] = new ShoppingSession(
					$id, $row['user_id'], []
				);
			}
			if (!is_null($row['item_id']))
			{
				$shoppingSessions[$row['id']]->addProduct(
					ProductRepositoryImpl::getById($row['item_id']),
					$row['quantities']
				);
			}
		}
		if (count($shoppingSessions) === 1)
		{
			return $shoppingSessions[$id];
		}

		return $shoppingSessions;
	}

	public static function add($userId, array $productsQuantities)
	{
		$query = Query::getInstance();
		try
		{
			$query->begin();
			$addNewShoppingSessionSQL = "INSERT INTO up_shopping_session (user_id) 
				VALUES ({$userId})";
			$query->getQueryResult($addNewShoppingSessionSQL);
			$last = $query->last();
			foreach ($productsQuantities as $product)
			{
				$addLinkToItemSQL = "INSERT INTO up_shopping_session_item (item_id, shopping_session_id, quantities)
									VALUES ({$product->info->id}, {$last}, {$product->getQuantity()})";
				$query->getQueryResult($addLinkToItemSQL);
			}
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw $e;
		}
	}

	public static function change(ShoppingSession $shoppingSession): void
	{
		$query = Query::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		$strProduct = implode(', ', self::getProductIds($shoppingSession->getProducts()));
		try
		{
			$query->begin();
			$changeProductSQL = "UPDATE up_shopping_session SET updated_at='{$now}' where id = {$shoppingSession->id}";
			$query->getQueryResult($changeProductSQL);
			$deleteProductSQL = "DELETE FROM up_shopping_session_item WHERE shopping_session_id={$shoppingSession->id} AND item_id NOT IN ({$strProduct})";
			$query->getQueryResult($deleteProductSQL);
			foreach ($shoppingSession->getProducts() as $item)
			{
				$addLinkToTagSQL = "INSERT IGNORE INTO up_shopping_session_item (item_id, shopping_session_id, quantities)
					VALUES ({$item->info->id}, {$shoppingSession->id}, {$item->getQuantity()})";
				$query->getQueryResult($addLinkToTagSQL);
			}
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw $e;
		}
	}

	public static function delete($id)
	{
		$query = Query::getInstance();
		try
		{
			$query->begin();
			$deleteLinkShoppingSessionSQL = "DELETE FROM up_shopping_session_item WHERE shopping_session_id=$id";
			$query->getQueryResult($deleteLinkShoppingSessionSQL);
			$deleteShoppingSessionSQL = "DELETE FROM up_shopping_session WHERE id=$id";
			$query->getQueryResult($deleteShoppingSessionSQL);
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
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
