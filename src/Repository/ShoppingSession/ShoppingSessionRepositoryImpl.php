<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\ShoppingSessionTable;

class ShoppingSessionRepositoryImpl implements ShoppingSessionRepository
{
	public static function getById(int $id): ShoppingSession
	{
		return self::createShoppingSessionList(
			self::getSpecialSessionList(['AND', ['=shopping_session_id' => $id]])
		)[$id];
	}

	public static function getAll(): array
	{
		return self::createShoppingSessionList(self::getSpecialSessionList());
	}

	public static function getByUser($id): ShoppingSession
	{
		$shoppingSession = array_values(
			self::createShoppingSessionList(self::getSpecialSessionList(['AND', ['=user_id' => $id]]))
		);
		if (empty($shoppingSession))
		{
			self::add($id, []);

			return self::getByUser($id);
		}

		return $shoppingSession[0];
	}

	private static function createShoppingSessionList(\mysqli_result $result): array
	{
		$shoppingSessions = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($shoppingSessions[$row['shopping_session_id']]))
			{
				$shoppingSessions[$row['shopping_session_id']] = self::createShoppingSessionEntity($row);
			}
			if (!is_null($row['id']))
			{
				$shoppingSessions[$row['shopping_session_id']]->addProduct(
					ProductRepositoryImpl::createProductEntity($row),
					$row['quantities']
				);
			}
		}

		return $shoppingSessions;
	}

	public static function add($userId, array $productsQuantities): void
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

	public static function createShoppingSessionEntity(array $row): ShoppingSession
	{
		return new ShoppingSession(
			$row['shopping_session_id'], $row['user_id'], []
		);
	}

	private static function getSpecialSessionList($where = []): \mysqli_result|bool
	{
		return ShoppingSessionTable::getList([
												 'shopping_session_id' => 'id',
											 ], [
												 'product' => [
													 'quantities',
													 'id',
													 'name',
													 'price',
													 'is_active',
												 ],
												 'image' => [
													 'image_id' => 'id',
													 'path',
												 ],
												 'user' => ['user_id' => 'id'],
											 ], conditions: $where);
	}
}
