<?php

namespace Up\Repository\Product;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Entity\Product;
use Up\Exceptions\Service\AdminService\ProductNotDisable;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\Query;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(int $page = 1): array
	{
		$query = Query::getInstance();
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.is_active = 1
	            LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getAllProducts(): array
	{
		$query = Query::getInstance();
		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.is_active = 1
	           ";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getAllForAdmin(int $page = 1): array
	{
		$query = Query::getInstance();
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
	            LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getById(int $id): Product
	{
		$query = Query::getInstance();
		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				inner join up_image on up_item.id = item_id
	            where up_item.id = {$id} AND up_item.is_active = 1";

		$result = $query->getQueryResult($sql);

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
				$imagePath = $row['path'];

				$isFirstLine = false;
			}
			else
			{
				$tags[] = TagRepositoryImpl::getById($row['tagId']);
				$imagePath = $row['path'];
			}
		}

		return new Product(
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt, $imagePath
		);

	}

	public static function getByTitle(string $title, int $page = 1): array
	{
		$query = Query::getInstance();
		$escapedTitle = $query->escape($title);

		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.name LIKE '%{$escapedTitle}%' AND up_item.is_active = 1
				LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function add(ProductAddingDto $productAddingDto): void
	{
		$query = Query::getInstance();
		try
		{
			$query->begin();
			$escapedTitle = $query->escape($productAddingDto->title);
			$escapedDescription = $query->escape($productAddingDto->description) ? : "NULL";

			$addNewProductSQL = "INSERT INTO up_item (name, description, price) 
				VALUES ('{$escapedTitle}', '{$escapedDescription}', {$productAddingDto->price})";
			$query->getQueryResult($addNewProductSQL);

			$lastItem = $query->last();
			$addImgNewProductSQL = "INSERT INTO up_image (path, item_id) VALUES ('{$productAddingDto->imagePath}', {$lastItem})";
			$query->getQueryResult($addImgNewProductSQL);
			TagRepositoryImpl::add($productAddingDto->tag);
			$lastTag = $query->last();
			$addTagNewProductSQL = "INSERT INTO up_item_tag (id_item, id_tag) VALUES ({$lastItem}, {$lastTag})";
			$query->getQueryResult($addTagNewProductSQL);
			//			$last = mysqli_insert_id($connection);
			//			foreach ($tags as $tag)
			//			{
			//				$addLinkToTagSQL = "INSERT INTO up_item_tag (id_item, id_tag) VALUES ({$last}, {$tag})";
			//				$query->getQueryResult($addLinkToTagSQL);
			//			}
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw $e;
		}
	}

	/*	public static function delete($id): void
		{
			$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
			try
			{
				mysqli_begin_transaction($connection);
				$query->getQueryResult($addNewProductSQL);
				mysqli_commit($connection);
			}
			catch (\Throwable $e)
			{
				mysqli_rollback($connection);
				throw $e;
			}
		}*/

	/**
	 * @throws ProductNotDisable
	 */
	public static function disable($id): void
	{
		$query = Query::getInstance();
		try
		{
			$disableProductSQL = "UPDATE up_item SET is_active=0 where id = {$id}";
			$result = $query->getQueryResult($disableProductSQL);
			if ($query->affectedRows() === 0)
			{
				throw new ProductNotDisable();
			}
		}
		catch (\Throwable)
		{
			throw new ProductNotDisable();
		}
	}

	public static function change(ProductChangeDto $productChangeDto): void
	{
		$query = Query::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');

		/*foreach ($tags as $tag)
		{
			$tagIds[] = $tag->id;
		}*/

		/*$strTags = implode(', ', $tagIds);*/
		$escapedName = $query->escape($productChangeDto->title);
		$escapedDescription = $query->escape($productChangeDto->description);
		try
		{
			$query->begin();
			$changeProductSQL = "UPDATE up_item SET name='{$escapedName}', description='{$escapedDescription}', price= {$productChangeDto->price}, edited_at='{$now}' where id = {$productChangeDto->id}";
			$query->getQueryResult($changeProductSQL);
			/*$deleteProductSQL = "DELETE FROM up_item_tag WHERE id_item={$productChangeDto->id} AND id_tag NOT IN ({$strTags})";
			$query->getQueryResult($deleteProductSQL);
			foreach ($tags as $tag)
			{
				$addLinkToTagSQL = "INSERT IGNORE INTO up_item_tag (id_item, id_tag) VALUES ({$id}, {$tag->id})";
				$query->getQueryResult($addLinkToTagSQL);
			}*/
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw $e;
		}
	}

	public static function getByTag(int $tagId, int $page = 1): array
	{
		$query = Query::getInstance();
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag it on up_item.id = it.id_item
				WHERE it.id_tag = {$tagId} AND up_item.is_active = 1
				LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getByTags(array $tags, int $page = 1): array
	{
		$query = Query::getInstance();
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		foreach ($tags as $tag)
		{
			$tagIds[] = $tag->id;
		}

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt, up_image.id as imageId, path
				from up_item
				inner join up_image on up_item.id = item_id
	            inner join up_item_tag it on up_item.id = it.id_item
				WHERE it.id_tag IN (" . implode(",", $tagIds) . ") AND up_item.is_active = 1
				LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	private static function createProductList(\mysqli_result $result): array
	{
		$products = [];
		try
		{
			$isFirstLine = true;
			while ($row = mysqli_fetch_assoc($result))
			{
				if (!isset($products[$row['id']]))
				{
					if (!$isFirstLine)
					{
						$products[$id] = new Product(
							$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt, $imagePath
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
					$imagePath = $row['path'];

					$isFirstLine = false;
				}
				else
				{
					$imagePath = $row['path'];
					$tags[] = TagRepositoryImpl::getById($row['tagId']);
				}
			}

			$products[] = new Product(
				@$id, @$name, @$description, @$price, @$tags, @$isActive, @$addedAt, @$editedAt, @$imagePath
			);
		}
		catch (\TypeError)
		{
			return [];
		}

		return $products;
	}

	public static function getColumn(): array
	{
		$query = Query::getInstance();
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'up_item';";

		$result = $query->getQueryResult($sql);

		$columns = [];

		while ($column = mysqli_fetch_column($result))
		{
			$columns[] = $column;
		}

		return $columns;
	}
}
