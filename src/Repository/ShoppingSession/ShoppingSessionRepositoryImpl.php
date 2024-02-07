<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\Order;
use Up\Entity\Product;
use Up\Entity\ProductQuantity;
use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\ShoppingSession\ShoppingSessionRepository;
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

}