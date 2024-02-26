<?php

namespace Up\Repository\Product;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Entity\Product;
use Up\Entity\ProductCharacteristic;
use Up\Exceptions\Admin\ProductNotAdd;
use Up\Exceptions\Admin\ProductNotChanged;
use Up\Exceptions\Admin\ProductNotDisabled;
use Up\Exceptions\Admin\ProductNotRestored;
use Up\Repository\ProductCharacteristic\ProductCharacteristicImpl;
use Up\Repository\SpecialOffer\SpecialOfferRepositoryImpl;
use Up\Exceptions\Images\ImageNotAdd;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\Orm;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\ProductTable;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');

		$offset = $limit * ($page - 1);
		$result = ProductTable::getList(['id'],
			conditions:                 ['AND', ['=is_active' => 1]],
			orderBy:                    ['priority' => 'ASC'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);

		return self::createProductList(self::getProductList(['AND', ['in=id' => $ids]]));
	}

	public static function getAllForAdmin(int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$result = ProductTable::getList(['id'],
			orderBy:                    ['priority' => 'ASC'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);

		return self::createProductList(self::getProductList(['AND', ['in=id' => $ids]]));
	}

	public static function getById(int $id): Product
	{
		return self::createProductList(self::getProductList(['AND', ['=id' => $id, '=is_active' => 1]]))[$id];
	}

	public static function getByTitle(string $title, int $page = 1): array
	{
		$orm = Orm::getInstance();
		$escapedTitle = $orm->escapeString($title);

		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$ids = ProductTable::getList(['id'],
			conditions:              ['AND', ['=is_active' => 1, '%=name' => $escapedTitle]],
			orderBy:                 ['priority' => 'ASC'],
			limit:                   $limit,
			offset:                  $offset);
		$result = self::getProductList(['AND', ['in=id' => $ids]]);

		return self::createProductList($result);
	}

	public static function getProductsBySpecialOffer(int $specialOfferId, int $page): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$ids = ProductTable::getList(['id'],
			conditions:              ['AND', ['=is_active' => 1, 'special_offer_id' => $specialOfferId]],
			orderBy:                 ['priority' => 'ASC'],
			limit:                   $limit,
			offset:                  $offset);
		$result = self::getProductList(['AND', ['in=id' => $ids]]);

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
			if ($query->affectedRows() === 0)
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
		catch (\Throwable $e)
		{
			$query->commit();
			throw $e;
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
					added_at, edited_at, up_image.id as image_id, path, up_item_special_offer.special_offer_id, priority
					from up_item
					left join up_image on up_item.id = item_id
					left join up_item_tag on up_item.id = up_item_tag.id_item
					left join up_item_special_offer on up_item_special_offer.item_id = up_item.id
					WHERE id_tag = {$tagId} AND up_item.is_active = 1
					ORDER BY priority
					LIMIT {$limit} OFFSET {$offset}";

			$result = $query->getQueryResult($sql);

			return self::createProductList($result);
		}*/

	public static function getByTags(array $tags, int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$result = ProductTable::getList(['id'],
			selectedRelatedColumns:     ['tag' => ['id_tag' => 'id']],
			conditions:                 ['AND', ['=is_active' => 1, 'in=id_tag' => $tags]],
			orderBy:                    ['priority' => 'ASC'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}
		$result = self::getProductList(['AND', ['in=id' => $ids]]);

		return self::createProductList($result);
	}

	private static function createProductList(\mysqli_result $result): array
	{
		$products = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($products[$row['id']]))
			{
				$products[$row['id']] = self::createProductEntity($row);
			}
			if (!is_null($row['id_tag']))
			{
				$products[$row['id']]->addTag(TagRepositoryImpl::createTagEntity($row));
			}
			if (!is_null($row['special_offer_id']))
			{
				$products[$row['id']]->addSpecialOffer(
					SpecialOfferRepositoryImpl::createSpecialOfferEntity($row)
				);
			}
			if (!is_null($row['characteristic_id']))
			{
				$products[$row['id']]->addCharacteristic(new ProductCharacteristic($row['characteristic_title'], $row['value']));
			}

			// if (!is_null($row['image_id']))
			// {
			// 	$products[$row['id']]->addImage(new Image($row['image_id'], $row['path'], 'main'));
			// }

		}

		return $products;
	}

	public static function createProductEntity(array $row): Product
	{
		$tag = [];
		$specialOffer = [];
		// $image = [new Image(404, '/images/imgNotFound.png', 'main')];
		$imagePath = '/images/imgNotFound.png';
		if (isset($row['image_id']))

		{
			if (!is_null($row['id_tag']))
			{
				// $image = [new Image($row['image_id'], $row['path'], 'main')];
				$imagePath = $row['path'];
			}
		}

		return new Product(
			$row['id'],
			$row['name'],
			$row['description'] ?? null,
			$row['price'],
			$tag,
			$row['is_active'] ?? null,
			$row['added_at'] ?? null,
			$row['edited_at'] ?? null,
			$imagePath,
			$specialOffer,
			$row['priority'] ?? null,
			[],
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

	public static function getLimitedProductsBySpecialOffer(int $specialOfferId): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PREVIEW');

		$result = ProductTable::getList(['id'],
			selectedRelatedColumns:     ['specialOffer' => ['special_offer_id' => 'id']],
			conditions:                 ['AND', ['=is_active' => 1, '=special_offer_id' => $specialOfferId]],
			orderBy:                    ['priority' => 'ASC'],
			limit:                      $limit);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}
		$result = self::getProductList(['AND', ['in=id' => $ids]]);

		return self::createProductList($result);
	}

	private static function getIds(\mysqli_result $result): array
	{
		$ids = [];
		while ($row = $result->fetch_assoc())
		{
			$ids[] = $row['id'];
		}

		return $ids;
	}

	private static function getProductList($where = []): \mysqli_result|bool
	{
		return ProductTable::getList([
										 'id',
										 'name',
										 'description',
										 'price',
										 'is_active',
										 'added_at',
										 'edited_at',
										 'priority',
									 ],
									 [
										 'image' => ['image_id' => 'id', 'path'],
										 'tag' => ['id_tag' => 'id', 'name_tag' => 'name'],
										 'specialOffer' => [
											 'special_offer_id' => 'id',
											 'special_offer_title' => 'title',
											 'special_offer_description' => 'description',
										 ],
										 'characteristic' => [
											 'characteristic_id' => 'id',
											 'characteristic_title' => 'title',
											 'value',
										 ],
									 ],
									 $where,
									 ['priority' => 'ASC']);
	}
}
