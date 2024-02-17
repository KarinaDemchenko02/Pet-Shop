<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ProductQuantity;
use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Database\Query;

class ShoppingSessionRepositoryImpl implements ShoppingSessionRepository
{

	public static function getById(int $id): ShoppingSession
	{
		$query = Query::getInstance();
		$sql = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				left join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id
				where id = {$id};";

		$result = $query->getQueryResult($sql);
		$products = [];

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$userId = $row['user_id'];
				$createdAt = $row['created_at'];
				$updatedAt = $row['updated_at'];
				$isFirstLine = false;
			}

			if ($row['item_id'] === null)
			{
				continue;
			}

			$products[$row['item_id']] = new ProductQuantity(
				ProductRepositoryImpl::getById($row['item_id']), $row['quantities']
			);

		}

		return new ShoppingSession(
			$id, $userId, $products, $createdAt, $updatedAt
		);
	}

	public static function getAll(): array
	{
		$query = Query::getInstance();
		$sql = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				inner join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id";

		$result = $query->getQueryResult($sql);

		return self::createShoppingSessionList($result);
	}

	public static function getByUser($id)
	{
		$query = Query::getInstance();
		$sql = "select id, user_id, item_id, quantities ,created_at, updated_at
				from up_shopping_session
				left join up_shopping_session_item on id = up_shopping_session_item.shopping_session_id
				where user_id = {$id};";

		$result = $query->getQueryResult($sql);
		$products = [];

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$userId = $row['user_id'];
				$createdAt = $row['created_at'];
				$updatedAt = $row['updated_at'];
				$isFirstLine = false;
			}

			if ($row['item_id'] === null)
			{
				continue;
			}

			$products[$row['item_id']] = new ProductQuantity(
				ProductRepositoryImpl::getById($row['item_id']), $row['quantities']
			);

		}
		if ($isFirstLine)
		{
			self::add($id, []);

			return self::getByUser($id);
		}

		return new ShoppingSession(
			$id, $userId, $products, $createdAt, $updatedAt
		);
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
						$id, $userId, $products, $createdAt, $updatedAt
					);
				}
				$id = $row['id'];
				$products[$row['item_id']] = new ProductQuantity(
					ProductRepositoryImpl::getById($row['item_id']), $row['quantities']
				);

				$userId = $row['user_id'];
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
			$id, $userId, $products, $createdAt, $updatedAt
		);

		return $ShoppingSessions;
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
