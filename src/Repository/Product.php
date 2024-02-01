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

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				if (!$isFirstLine)
				{
					$products[$id] = new Models\Product(
						$id,
						$name,
						$description,
						$price,
						$tags,
						$isActive,
						$addedAt,
						$editedAt
					);
				}
				$id = $row['id'];
				$name = $row['name'];
				$description = $row['description'];
				$price = $row['price'];
				$tags = [Tag::getById($row['tagId'])];
				$isActive = $row['isActive'];
				$addedAt = $row['addedAt'];
				$editedAt = $row['editedAt'];

				$isFirstLine = false;
			}
			else
			{
				$tags[]=Tag::getById($row['tagId']);
			}
		}

		$products[$id] = new Models\Product(
			$id,
			$name,
			$description,
			$price,
			$tags,
			$isActive,
			$addedAt,
			$editedAt
		);

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

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if ($isFirstLine)
			{
				$id = $row['id'];
				$name = $row['name'];
				$description = $row['description'];
				$price = $row['price'];
				$tags = [Tag::getById($row['tagId'])];
				$isActive = $row['isActive'];
				$addedAt = $row['addedAt'];
				$editedAt = $row['editedAt'];

				$isFirstLine = false;
			}
			else
			{
				$tags[] = Tag::getById($row['tagId']);
			}
		}
		$product = new Models\Product(
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt
		);

		return $product;

	}
}