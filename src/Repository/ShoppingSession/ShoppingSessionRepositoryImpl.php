<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ProductQuantity;
use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Database\Orm;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\ShoppingSessionProductTable;
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
		$orm = Orm::getInstance();
		try
		{
			$orm->begin();
			ShoppingSessionTable::add(['user_id' => $userId]);
			$last = $orm->last();
			/** @var ProductQuantity $product */
			foreach ($productsQuantities as $product)
			{
				ShoppingSessionProductTable::add(
					[
						'product_id' => $product->info->id,
						'shopping_session_id' => $last,
						'quantities' => $product->getQuantity(),
					]
				);
			}
			$orm->commit();
		}
		catch (\Throwable $e)
		{
			$orm->rollback();
			throw $e;
		}
	}

	public static function change(ShoppingSession $shoppingSession): void
	{
		$orm = Orm::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		try
		{
			$orm->begin();
			ShoppingSessionTable::update(['updated_at' => $now], ['AND', ['=id' => $shoppingSession->id]]);
			ShoppingSessionProductTable::delete(
				[
					'AND',
					[
						'shopping_session_id' => $shoppingSession->id,
						'!in=product_id' => self::getProductIds($shoppingSession->getProducts()),
					],
				]
			);
			foreach ($shoppingSession->getProducts() as $item)
			{
				ShoppingSessionProductTable::add(
					[
						'product_id' => $item->info->id,
						'shopping_session_id' => $shoppingSession->id,
						'quantities' => $item->getQuantity(),
					],
					true
				);
			}
			$orm->commit();
		}
		catch (\Throwable $e)
		{
			$orm->rollback();
			throw $e;
		}
	}

	public static function delete($id)
	{
		$orm = Orm::getInstance();
		try
		{
			$orm->begin();
			ShoppingSessionProductTable::delete(['AND', ['=shopping_session_id' => $id]]);
			ShoppingSessionTable::delete(['AND', ['=id' => $id]]);
			$orm->commit();
		}
		catch (\Throwable $e)
		{
			$orm->rollback();
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
