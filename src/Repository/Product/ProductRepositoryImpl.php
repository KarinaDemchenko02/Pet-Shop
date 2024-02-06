<?php

namespace Up\Repository\Product;

use Up\Entity\Product;
use Up\Repository\RepositoryImpl;
use Up\Repository\Tag\TagRepositoryImpl;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(): array
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item";

		$result = RepositoryImpl::getResultSQLQuery($sql);

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

	public static function getById(int $id): Product
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
	            where up_item.id = {$id}";

		$result = RepositoryImpl::getResultSQLQuery($sql);

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
}
