<?php

namespace Up\Repository\Session;

use Up\Entity\Cart;
use Up\Entity\ShoppingSession;
use Up\Util\Database\QueryResult;

class ShoppingSessionImpl implements ShoppingSessionRepository
{

	public static function getAll(): array
	{
		// TODO: AAAA
	}

	public static function getById(int $id): ShoppingSession
	{
		// TODO: Implement getById() method.
	}

	public static function add($userId, Cart $cart)
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		try
		{
			mysqli_begin_transaction($connection);
			$addNewShoppingSessionSQL = "INSERT INTO up_shopping_session (user_id) 
				VALUES ($userId)";
			QueryResult::getQueryResult($addNewShoppingSessionSQL);
			$last = mysqli_insert_id($connection);
			foreach ($cart->products as $product)
			{
				$addLinkToItemSQL = "INSERT INTO up_shopping_session_item (item_id, shopping_session_id, quantities)
									VALUES ({$product[0]->id}, {$last}, $product[1])";
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

	public static function change($id, Cart $cart): void
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		$strProduct = implode(', ', $cart->getProductIds());
		try
		{
			mysqli_begin_transaction($connection);
			$changeProductSQL = "UPDATE up_shopping_session SET   where id = {$id}";
			QueryResult::getQueryResult($changeProductSQL);
			$deleteProductSQL = "DELETE FROM up_item_tag WHERE id_item={$id} AND id_tag NOT IN ({$strTags})";
			QueryResult::getQueryResult($deleteProductSQL);
			foreach ($tags as $tag)
			{
				$addLinkToTagSQL = "INSERT IGNORE INTO up_item_tag (id_item, id_tag) VALUES ({$id}, {$tag})";
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

}