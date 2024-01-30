<?php

namespace Up\Repository;

use Up\Repository\Repository;
use Up\Service\Database;
use Up\Models;

class Product extends Repository
{

	public static function getAll(): array
	{
		$database = new Database();
		$connection = $database->getDbConnection();

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$products = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				$tag = Tag::getById($row['tagId']);
				$products[$row['id']] = new Models\Product(
					$row['id'],
					$row['name'],
					$row['description'],
					$row['price'],
					$tag,
					$row['isActive'],
					$row['addedAt'],
					$row['editedAt']
				);
			}
			else
			{
				$tag = Tag::getById($row['tagId']);
				$products[$row['id']]->addTag($tag);
			}

		}

		return $products;

	}

	public static function getById(int $id): Models\Product
	{
		$database = new Database();
		$connection = $database->getDbConnection();

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
	            where up_item.id = {$id}";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($product))
			{
				$tag = Tag::getById($row['tagId']);
				$tags = [$tag];
				$product = new Models\Product(
					$row['id'],
					$row['name'],
					$row['description'],
					$row['price'],
					$tag,
					$row['isActive'],
					$row['addedAt'],
					$row['editedAt']
				);
			}
			else
			{
				$tag = Tag::getById($row['tagId']);
				$product->addTag($tag);
			}

		}

		return $product;

	}
}