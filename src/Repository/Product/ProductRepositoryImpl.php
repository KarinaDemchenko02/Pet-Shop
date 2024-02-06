<?php

namespace Up\Repository\Product;

use Up\Entity\Product;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\QueryResult;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(): array
	{
		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item";

		$result = QueryResult::getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getById(int $id): Product
	{

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
	            where up_item.id = {$id}";

		$result = QueryResult::getQueryResult($sql);

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$id = $row['id'];
				$name = $row['name'];
				$description = $row['description'];
				$price = $row['price'];
				$tags = [TagRepositoryImpl::getById($row['tagId'])];
				$isActive = $row['isActive'];
				$addedAt = $row['addedAt'];
				$editedAt = $row['editedAt'];

				$isFirstLine = false;
			}
			else
			{
				$tags[] = TagRepositoryImpl::getById($row['tagId']);
			}
		}
		$product = new Product(
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt
		);

		return $product;

	}

	public static function getByTitle(string $title): array
	{
		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
				added_at as addedAt, edited_at as editedAt
				from up_item
				inner join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.name LIKE '%{$title}%'";

		$result = QueryResult::getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function add(Product $product): bool
	{


		$addNewProductSQL = "INSERT INTO up_item (name, description, price, added_at, edited_at, is_active) 
				VALUES ('{$product->title}', '{$product->description}', {$product->price}, 
				        '{$product->addedAt}', '{$product->editedAt}', {$product->isActive})";

		$addLinkToTagSQL = "INSERT INTO up_item_tag (id_item, id_tag) VALUES (2, 3)";

		QueryResult::getQueryResult($addNewProductSQL);

		return true;
	}

	public static function getByTags(array $tags): array
	{
		foreach ($tags as $tag)
		{
			$tagIds[] = $tag->id;
		}

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive,
				added_at as addedAt, edited_at as editedAt
				from up_item
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
						$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt
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

				$isFirstLine = false;
			}
			else
			{
				$tags[] = TagRepositoryImpl::getById($row['tagId']);
			}
		}

		$products[$id] = new Product(
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt
		);

		return $products;
	}
}
