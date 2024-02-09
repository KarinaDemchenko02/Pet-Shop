<?php

namespace Up\Repository\Product;

use Up\Entity\Image;
use Up\Entity\Product;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\QueryResult;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(): array
	{
		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item";

		$result = QueryResult::getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getById(int $id): Product
	{

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				inner join up_image on up_item.id = item_id
	            where up_item.id = {$id}";

		$result = QueryResult::getQueryResult($sql);

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$id = $row['id'];
			}
			if ($isFirstLine)
			{
				$name = $row['name'];
				$description = $row['description'];
				$price = $row['price'];
				$tags = [TagRepositoryImpl::getById($row['tagId'])];
				$isActive = $row['isActive'];
				$addedAt = $row['addedAt'];
				$editedAt = $row['editedAt'];
				$images = [new Image($row['imageId'], $row['path'], 'characteristic')];

				$isFirstLine = false;
			}
			else
			{
				$tags[] = TagRepositoryImpl::getById($row['tagId']);
				$images[] = new Image($row['imageId'], $row['path'], 'characteristic');
			}
		}
		$product = new Product(
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt, $images
		);

		return $product;

	}

	public static function getByTitle(string $title): array
	{
		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.name LIKE '%{$title}%'";

		$result = QueryResult::getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function add($title, $description, $price, $tags): void
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		try
		{
			mysqli_begin_transaction($connection);
			$description = $description ?: "NULL";

			$addNewProductSQL = "INSERT INTO up_item (name, description, price) 
				VALUES ('{$title}', '{$description}', {$price})";
			QueryResult::getQueryResult($addNewProductSQL);
			$last = mysqli_insert_id($connection);
			foreach ($tags as $tag)
			{
				$addLinkToTagSQL = "INSERT INTO up_item_tag (id_item, id_tag) VALUES ({$last}, {$tag})";
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

	/*	public static function delete($id): void
		{
			$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
			try
			{
				mysqli_begin_transaction($connection);
				QueryResult::getQueryResult($addNewProductSQL);
				mysqli_commit($connection);
			}
			catch (\Throwable $e)
			{
				mysqli_rollback($connection);
				throw $e;
			}
		}*/

	public static function disable($id): void
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		try
		{
			mysqli_begin_transaction($connection);
			$disableProductSQL = "UPDATE up_item SET is_active=0 where id = {$id}";
			QueryResult::getQueryResult($disableProductSQL);
			mysqli_commit($connection);
		}
		catch (\Throwable $e)
		{
			mysqli_rollback($connection);
			throw $e;
		}
	}

	public static function change($id, $name, $description, $price, $tags): void
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		$strTags = implode(', ', $tags);
		try
		{
			mysqli_begin_transaction($connection);
			$changeProductSQL = "UPDATE up_item SET name='{$name}', description='{$description}', price=$price, edited_at='{$now}'  where id = {$id}";
			QueryResult::getQueryResult($changeProductSQL);
			$deleteProductSQL = "DELETE FROM up_item_tag WHERE id_item={$id} AND id_tag NOT IN ({$strTags})";
			QueryResult::getQueryResult($deleteProductSQL);
			foreach ($tags as $tag) {
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

	public static function getByTags(array $tags): array
	{
		foreach ($tags as $tag) {
			$tagIds[] = $tag->id;
		}

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE it.id_tag IN (" . implode(",", $tagIds) . ")";

		$result = QueryResult::getQueryResult($sql);

		return self::createProductList($result);
	}

	private static function createProductList(\mysqli_result $result): array
	{
		$products = [];

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				if (!$isFirstLine)
				{
					$products[$id] = new Product(
						$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt, $images
					);
				}
				$id = $row['id'];
				$name = $row['name'];
				$description = $row['description'];
				$price = $row['price'];
				$tags = [TagRepositoryImpl::getById($row['tagId'])];
				$isActive = $row['isActive'];
				$addedAt = $row['addedAt'];
				$editedAt = $row['editedAt'];
				$images = [new Image($row['imageId'], $row['path'], 'characteristic')];

				$isFirstLine = false;
			}
			else
			{
				$images[] = new Image($row['imageId'], $row['path'], 'characteristic');
				$tags[] = TagRepositoryImpl::getById($row['tagId']);
			}
		}

		new Product(
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt, $images
		);

		return $products;
	}

	public static function getColumn(): array
	{
		$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'up_item';";

		$result = QueryResult::getQueryResult($sql);

		$columns = [];

		while ($column = mysqli_fetch_column($result))
		{
			$columns[] = $column;
		}

		return $columns;
	}
}
