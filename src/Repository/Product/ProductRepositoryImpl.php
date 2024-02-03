<?php

<<<<<<<< HEAD:src/Repository/ProductRepository/ProductRepositoryImpl.php
namespace Up\Repository\ProductRepository;

namespace Up\Repository\ProductRepository;


use Up\Entity\Product;
use Up\Repository\TagRepository\TagRepositoryImpl;

========
namespace Up\Repository\Product;

use Up\Models;
use Up\Repository\Tag\TagRepositoryImpl;

>>>>>>>> f3757b0 (added interfaces to the repository):src/Repository/Product/ProductRepositoryImpl.php
class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(): array
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Configuration::getInstance()->option('DB_HOST'),
			\Up\Util\Configuration::getInstance()->option('DB_USER'),
			\Up\Util\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \Exception(mysqli_error($connection));
		}

		$products = [];

		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				if (!$isFirstLine)
				{
<<<<<<<< HEAD:src/Repository/ProductRepository/ProductRepositoryImpl.php
					$products[$id] = new Product(
========
					$products[$id] = new Models\Product(
>>>>>>>> f3757b0 (added interfaces to the repository):src/Repository/Product/ProductRepositoryImpl.php
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

<<<<<<<< HEAD:src/Repository/ProductRepository/ProductRepositoryImpl.php
		$products[$id] = new Product(
========
		$products[$id] = new Models\Product(
>>>>>>>> f3757b0 (added interfaces to the repository):src/Repository/Product/ProductRepositoryImpl.php
			$id, $name, $description, $price, $tags, $isActive, $addedAt, $editedAt
		);

		return $products;

	}

	public static function getById(int $id): Product
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Configuration::getInstance()->option('DB_HOST'),
			\Up\Util\Configuration::getInstance()->option('DB_USER'),
			\Up\Util\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select up_item.id, up_item.name, description, price, id_tag as tagId, is_active as isActive, 
                added_at as addedAt, edited_at as editedAt
				from up_item
	            inner join up_item_tag on up_item.id = up_item_tag.id_item
	            where up_item.id = {$id}";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \Exception(mysqli_error($connection));
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
