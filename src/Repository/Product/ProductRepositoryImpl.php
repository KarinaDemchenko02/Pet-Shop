<?php

namespace Up\Repository\Product;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Entity\Product;
use Up\Exceptions\Admin\ProductNotAdd;
use Up\Exceptions\Admin\ProductNotChanged;
use Up\Exceptions\Admin\ProductNotDisabled;
use Up\Exceptions\Admin\ProductNotRestored;
use Up\Exceptions\Images\ImageNotAdd;
use Up\Exceptions\Product\ProductNotFound;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\Query;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(int $page = 1): array
	{
		$query = Query::getInstance();
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');

		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
				left join up_image on up_item.id = item_id
	            left join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.is_active = 1
	            LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	public static function getAllProducts(): array
	{
		$query = Query::getInstance();

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
				left join up_image on up_item.id = item_id
	            left join up_item_tag on up_item.id = up_item_tag.id_item
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

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
				left join up_image on up_item.id = item_id
	            left join up_item_tag on up_item.id = up_item_tag.id_item
	            LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	/**
	 * @throws ProductNotFound
	 */
	public static function getById(int $id, bool $showHiddenProducts = false): Product
	{
		$query = Query::getInstance();
		$status = '';
		if (!$showHiddenProducts)
		{
			$status =  'AND up_item.is_active = 1';
		}

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
	            left join up_item_tag on up_item.id = up_item_tag.id_item
				left join up_image on up_item.id = item_id
	            where up_item.id = {$id} {$status}";

		$result = $query->getQueryResult($sql);
		$product = null;
		$isFirstLine = true;
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($product))
			{
				$product = self::createProductEntity($row);
			}
			else
			{
				if (!is_null($row['id_tag']))
				{
					$product->addTag(TagRepositoryImpl::getById($row['id_tag']));
				}
				// if (!is_null($row['imageId']))
				// {
				// 	$product->addImage(new Image($row['imageId'], $row['path'], 'main'));
				// }
			}
		}
		if (is_null($product))
		{
			throw new ProductNotFound();
		}
		return $product;
	}

	public static function getByTitle(string $title, int $page = 1): array
	{
		$query = Query::getInstance();
		$escapedTitle = $query->escape($title);

		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
				left join up_image on up_item.id = item_id
	            left join up_item_tag on up_item.id = up_item_tag.id_item
				WHERE up_item.name LIKE '%{$escapedTitle}%' AND up_item.is_active = 1
				LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	/**
	 * @throws ProductNotAdd
	 */
	public static function add(ProductAddingDto $productAddingDto): int
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

			foreach ($productAddingDto->tags as $tag)
			{
				$addLinkToTagSQL = "INSERT INTO up_item_tag (id_item, id_tag) VALUES ({$lastItem}, {$tag})";
				$query->getQueryResult($addLinkToTagSQL);
			}
			$query->commit();
			return $lastItem;
		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new ProductNotAdd();
		}
	}

	/**
	 * @throws ProductNotDisabled
	 */
	public static function disable($id): void
	{
		$query = Query::getInstance();
		try
		{
			$disableProductSQL = "UPDATE up_item SET is_active=0 where id = {$id}";
			$query->getQueryResult($disableProductSQL);
			if (Query::affectedRows() === 0)
			{
				throw new ProductNotDisabled();
			}
		}
		catch (\Throwable)
		{
			throw new ProductNotDisabled();
		}
	}

	/**
	 * @throws ProductNotRestored
	 */
	public static function restore($id): void
	{
		$query = Query::getInstance();
		try
		{
			$restoreProductSQL = "UPDATE up_item SET is_active=1 where id = {$id}";
			$query->getQueryResult($restoreProductSQL);
			if (Query::affectedRows() === 0)
			{
				throw new ProductNotRestored();
			}
		}
		catch (\Throwable)
		{
			throw new ProductNotRestored();
		}
	}

	/**
	 * @throws ImageNotAdd
	 */
	public static function addImage(string $imagePath, int $id): void
	{
		$query = Query::getInstance();
		try
		{
			$escapedImage = $query->escape($imagePath);

			$addImageSQL = "UPDATE up_image SET path='{$escapedImage}' where item_id = {$id}";
			$query->getQueryResult($addImageSQL);
			if (Query::affectedRows() === 0)
			{
				throw new ImageNotAdd();
			}
		}
		catch (\Throwable)
		{
			throw new ImageNotAdd();
		}
	}

	/**
	 * @throws ProductNotChanged
	 */
	public static function change(ProductChangeDto $productChangeDto): void
	{
		$query = Query::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');

		$tagsChange = $productChangeDto->tags;
		$tagsExisting = self::getTagByProduct($productChangeDto->id);

		$tags = array_diff($tagsChange, $tagsExisting);

		$strTags = implode(', ', $productChangeDto->tags);

		$escapedName = $query->escape($productChangeDto->title);
		$escapedDescription = $query->escape($productChangeDto->description);
		try
		{
			$query->begin();
			$changeProductSQL = "UPDATE up_item SET name='{$escapedName}', description='{$escapedDescription}', price= {$productChangeDto->price}, edited_at='{$now}' where id = {$productChangeDto->id}";
			$query->getQueryResult($changeProductSQL);
			if (Query::affectedRows() === 0)
			{
				throw new ProductNotRestored();
			}

			$deleteProductSQL = "DELETE FROM up_item_tag WHERE id_item={$productChangeDto->id} AND id_tag NOT IN ({$strTags})";
			$query->getQueryResult($deleteProductSQL);
			foreach ($tags as $tag)
			{
				$addLinkToTagSQL = "INSERT INTO up_item_tag (id_item, id_tag) VALUES ({$productChangeDto->id}, {$tag})";
				$query->getQueryResult($addLinkToTagSQL);
			}
			$query->commit();
		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new ProductNotChanged();
		}
	}

	public static function getTagByProduct(int $idProduct): array
	{
		$query = Query::getInstance();

		$sql = "SELECT id_tag FROM `up_item_tag` WHERE id_item = {$idProduct}";

		$result = $query->getQueryResult($sql);

		$tagsId = [];

		while ($id = mysqli_fetch_column($result))
		{
			$tagsId[] = $id;
		}

		return $tagsId;
	}

	public static function getByTag(int $tagId, int $page = 1): array
	{
		$query = Query::getInstance();
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
				left join up_image on up_item.id = item_id
	            left join up_item_tag it on up_item.id = it.id_item
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

		$sql = "select up_item.id, up_item.name, description, price, id_tag, is_active,
                added_at, edited_at, up_image.id as imageId, path
				from up_item
				left join up_image on up_item.id = item_id
	            left join up_item_tag it on up_item.id = it.id_item
				WHERE it.id_tag IN (" . implode(",", $tags) . ") AND up_item.is_active = 1
				LIMIT {$limit} OFFSET {$offset}";

		$result = $query->getQueryResult($sql);

		return self::createProductList($result);
	}

	private static function createProductList(\mysqli_result $result): array
	{
		$query = Query::getInstance();
		$products = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				$products[$row['id']] = self::createProductEntity($row);
			}
			else
			{
				if (!is_null($row['id_tag']))
				{
					$products[$row['id']]->addTag(TagRepositoryImpl::getById($row['id_tag']));
				}
				// if (!is_null($row['imageId']))
				// {
				// 	$products[$row['id']]->addImage(new Image($row['imageId'], $row['path'], 'main'));
				// }
			}
		}

		return $products;
	}

	private static function createProductEntity(array $row): Product
	{
		$query = Query::getInstance();
		$tag = [];
		// $image = [new Image(404, '/images/imgNotFound.png', 'main')];
		$imagePath = '/images/imgNotFound.png';

		if (!is_null($row['id_tag']))
		{
			$tag = [TagRepositoryImpl::getById($row['id_tag'])];
		}
		if (!is_null($row['imageId']))
		{
			// $image = [new Image($row['imageId'], $row['path'], 'main')];
			$imagePath = $row['path'];
		}

		return new Product(
			$row['id'],
			$row['name'],
			$row['description'],
			$row['price'],
			$tag,
			$row['is_active'],
			$row['added_at'],
			$row['edited_at'],
			$imagePath
		);
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
