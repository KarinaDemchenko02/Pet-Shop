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
use Up\Repository\Image\ImageRepositoryImpl;
use Up\Repository\SpecialOffer\SpecialOfferRepositoryImpl;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\Orm;
use Up\Util\Database\Tables\ProductTable;
use Up\Util\Database\Tables\ProductTagTable;

class ProductRepositoryImpl implements ProductRepository
{
	public static function getAll(int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');

		$offset = $limit * ($page - 1);
		$result = ProductTable::getList(['id'],
			conditions:                 ['AND', ['=is_active' => 1]],
			orderBy:                    ['priority' => 'DESC'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}

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
		if (empty($ids))
		{
			return [];
		}

		return self::createProductList(self::getAllProductList(['AND', ['in=id' => $ids]]));
	}

	public static function getById(int $id): Product
	{
		return self::createProductList(self::getAllProductList(['AND', ['=id' => $id, '=is_active' => 1]]))[$id];
	}

	public static function getByTitle(string $title, int $page = 1): array
	{

		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);
		$title = urldecode($title);
		$ids = self::getIds(
			ProductTable::getList(['id'],
				conditions:       ['AND', ['=is_active' => 1, '%=name' => $title]],
				orderBy:          ['priority' => 'ASC'],
				limit:            $limit,
				offset:           $offset)
		);
		if (empty($ids))
		{
			return [];
		}
		$result = self::getProductList(['AND', ['in=id' => $ids]]);

		return self::createProductList($result);
	}

	public static function getProductsBySpecialOffer(int $specialOfferId, int $page): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);
		$ids = self::getIds(
			ProductTable::getList(['id', 'product_special_offer' => ['special_offer_id']],
				conditions:              ['AND', ['=is_active' => 1, '=special_offer_id' => $specialOfferId]],
				orderBy:                 ['priority' => 'ASC'],
				limit:                   $limit,
				offset:                  $offset)
		);

		if (empty($ids))
		{
			return [];
		}

		$result = self::getAllProductList(['AND', ['in=id' => $ids]]);


		return self::createProductList($result);
	}

	/**
	 * @throws ProductNotAdd
	 */
	public static function add(ProductAddingDto $productAddingDto): int
	{
		$orm = Orm::getInstance();
		try
		{
			$orm->begin();
			$description = $productAddingDto->description ? : "NULL";
			ProductTable::add(
				['name' => $productAddingDto->title, 'description' => $description, 'price' => $productAddingDto->price]
			);
			$lastItem = $orm->last();
			ImageRepositoryImpl::add($productAddingDto->imagePath, $lastItem);
			foreach ($productAddingDto->tags as $tag)
			{
				ProductTagTable::add(['product_id' => $lastItem, 'tag_id' => $tag]);
			}
			$orm->commit();

			return $lastItem;
		}
		catch (\Throwable $e)
		{
			$orm->rollback();
			throw $e;
		}
	}

	/**
	 * @throws ProductNotDisabled
	 */
	public static function disable($id): void
	{
		$orm = Orm::getInstance();
		ProductTable::update(['is_active' => 0], ['AND', ['=id' => $id]]);
		if ($orm->affectedRows() === 0)
		{
			throw new ProductNotDisabled();
		}
	}

	/**
	 * @throws ProductNotRestored
	 */
	public static function restore($id): void
	{
		$orm = Orm::getInstance();
		ProductTable::update(['is_active' => 1], ['AND', ['id' => $id]]);
		if ($orm->affectedRows() === 0)
		{
			throw new ProductNotRestored();
		}
	}

	/**
	 * @throws ProductNotChanged
	 */
	public static function change(ProductChangeDto $productChangeDto): void
	{
		$orm = Orm::getInstance();
		$time = new \DateTime();
		$now = $time->format('Y-m-d H:i:s');
		try
		{
			$orm->begin();
			ProductTable::update(
				[
					'name' => $productChangeDto->title,
					'description' => $productChangeDto->description,
					'price' => $productChangeDto->price,
					'edited_at' => $now,
					'priority' => $productChangeDto->priority,
				], ['AND', ['=id' => $productChangeDto->id]]
			);

			ProductTagTable::delete(
				['AND', ['!in=tag_id' => $productChangeDto->tags, '=product_id' => $productChangeDto->id]]
			);
			foreach ($productChangeDto->tags as $tagId)
			{
				ProductTagTable::add(['product_id' => $productChangeDto->id, 'tag_id' => $tagId], true);
			}
			/*$deleteProductSQL = "DELETE FROM up_item_tag WHERE id_item={$productChangeDto->id} AND tag_id NOT IN ({$strTags})";
			QueryResult::getQueryResult($deleteProductSQL);
			foreach ($tags as $tag)
			{
				$addLinkToTagSQL = "INSERT IGNORE INTO up_item_tag (id_item, tag_id) VALUES ({$id}, {$tag->id})";
				QueryResult::getQueryResult($addLinkToTagSQL);
			}*/

			if ($orm->affectedRows() === 0)
			{
				throw new ProductNotChanged();
			}
			$orm->commit();
		}
		catch (\Throwable $e)
		{
			$orm->commit();
			throw $e;
		}
	}

	/*	public static function getByTag(int $tagId, int $page = 1): array
		{
			$query = Query::getInstance();
			$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
			$offset = $limit * ($page - 1);

			$sql = "select up_item.id, up_item.name, description, price, tag_id, is_active,
					added_at, edited_at, up_image.id as image_id, path, up_item_special_offer.special_offer_id, priority
					from up_item
					left join up_image on up_item.id = item_id
					left join up_item_tag on up_item.id = up_item_tag.id_item
					left join up_item_special_offer on up_item_special_offer.item_id = up_item.id
					WHERE tag_id = {$tagId} AND up_item.is_active = 1
					ORDER BY priority
					LIMIT {$limit} OFFSET {$offset}";

			$result = $query->getQueryResult($sql);

			return self::createProductList($result);
		}*/

	public static function getByTags(array $tags, int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$result = ProductTable::getList(['id', 'product_tag' => ['tag_id']],
			conditions:                 ['AND', ['=is_active' => 1, 'in=tag_id' => $tags]],
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
			if (isset($row['tag_id']) && !is_null($row['tag_id']))
			{
				$products[$row['id']]->addTag(TagRepositoryImpl::createTagEntity($row));
			}
			if (isset($row['special_offer_id']) && !is_null($row['special_offer_id']))
			{
				$products[$row['id']]->addSpecialOffer(
					SpecialOfferRepositoryImpl::createSpecialOfferEntity($row)
				);
			}
			if (isset($row['characteristic_id']) && !is_null($row['characteristic_id']))
			{
				$products[$row['id']]->addCharacteristic(
					new ProductCharacteristic($row['characteristic_title'], $row['value'])
				);
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
			// $image = [new Image($row['image_id'], $row['path'], 'main')];
			$imagePath = $row['path'];
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

	public static function getLimitedProductsBySpecialOffer(int $specialOfferId): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PREVIEW');

		$result = ProductTable::getList(['id', 'special_offer' => ['special_offer_id' => 'id']],
			conditions:                 ['AND', ['=is_active' => 1, '=special_offer_id' => $specialOfferId]],
			orderBy:                    ['priority' => 'ASC'],
			limit:                      $limit);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}
		$result = self::getAllProductList(['AND', ['in=id' => $ids]]);

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

	private static function getAllProductList($where = []): \mysqli_result|bool
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
										 'image' => ['image_id' => 'id', 'path'],
										 'product_tag' => ['tag' => ['tag_id' => 'id', 'tag_title' => 'title']],
										 'product_special_offer' => [
											 'special_offer' => [
												 'special_offer_id' => 'id',
												 'special_offer_title' => 'title',
												 'special_offer_description' => 'description',
												 'special_offer_start_date' => 'start_date',
												 'special_offer_end_date' => 'end_date',
											 ],
										 ],
										 'product_characteristic' => [
											 'characteristic' => [
												 'characteristic_id' => 'id',
												 'characteristic_title' => 'title',
											 ],
											 'value',
										 ],
									 ],
									 $where,
									 ['priority' => 'ASC']);
	}

	public static function getProductList($where = []): \mysqli_result|bool
	{
		return ProductTable::getList([
										 'id',
										 'name',
										 'description',
										 'price',
										 'is_active',
										 'priority',
										 'image' => ['image_id' => 'id', 'path'],
									 ],
									 $where,
									 ['priority' => 'DESC']);
	}
}
