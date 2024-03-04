<?php

namespace Up\Repository\SpecialOffer;

use Up\Entity\SpecialOffer;
use Up\Entity\SpecialOfferPreviewProducts;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\SpecialOfferTable;

class SpecialOfferRepositoryImpl implements SpecialOfferRepository
{
	public static function getAll(): array
	{
		return self::createSpecialOfferList(self::getSpecialOfferList());
	}

	public static function getById(int $id): SpecialOffer
	{
		return self::createSpecialOfferList(self::getSpecialOfferList(['AND', ['=special_offer_id' => $id]]))[$id];
	}

	public static function add(string $title, string $description): bool
	{
		$query = Query::getInstance();
		$sql = "INSERT INTO up_special_offer (title, description) VALUES ('{$title}', '{$description}');";

		$result = $query->getQueryResult($sql);

		return true;
	}

	public static function getPreviewProducts(): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PREVIEW');
		$result = SpecialOfferTable::getList(['special_offer_id' => 'id']);
		$ids = self::getIds($result);

		$time = new \DateTime();
		$now = $time->format('Y-m-d');

		$result = SpecialOfferTable::getList(
			[
				'special_offer_id' => 'id',
				'special_offer_title' => 'title',
				'special_offer_description' => 'description',
				'special_offer_start_date' => 'start_date',
				'special_offer_end_date' => 'end_date',
				'product_special_offer' => [
					'product' => [
						'id',
						'name',
						'description',
						'price',
						'is_active',
						'priority',
						'image' => ['image_id' => 'id', 'path'],
					],
				],
			],
			['AND',
			 [
				 '=is_active' => 1,
				 'in=special_offer_id' => $ids,
				 '>=special_offer_start_date' => $now,
				 '<=special_offer_end_date' => $now,
			 ],
			],
			['priority' => 'ASC'],
		);
		$specialOfferPreviewProducts = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			if (!isset($specialOfferPreviewProducts[$row['special_offer_id']]))
			{
				$specialOfferPreviewProducts[$row['special_offer_id']] = new SpecialOfferPreviewProducts(
					self::createSpecialOfferEntity($row), []
				);
			}
			$specialOfferPreviewProduct = $specialOfferPreviewProducts[$row['special_offer_id']];
			$specialOffer = $specialOfferPreviewProduct->specialOffer;
			$product = null;
			if (!is_null($row['id']))
			{
				if (!isset($specialOfferPreviewProduct->getProducts()[$row['id']]))
				{
					$specialOfferPreviewProduct->addProduct(
						ProductRepositoryImpl::createProductEntity($row)
					);
				}
				if (isset($specialOfferPreviewProduct->getProducts()[$row['id']]))
				{
					$product = $specialOfferPreviewProduct->getProducts()[$row['id']];
				}
			}
			if (
				!is_null($row['special_offer_id']) && !is_null($product)
				&& !isset(
					$product->getSpecialOffer()[$row['special_offer_id']]
				)
			)
			{
				$product->addSpecialOffer($specialOffer);
			}
		}

		return $specialOfferPreviewProducts;
	}

	/*	public static function getLimitedProductsBySpecialOffer(int $specialOfferId): array
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
		}*/

	public static function createSpecialOfferEntity(array $row): SpecialOffer
	{
		return new SpecialOffer(
			$row['special_offer_id'],
			$row['special_offer_title'],
			$row['special_offer_description'],
			$row['special_offer_start_date'],
			$row['special_offer_end_date'],
		);
	}

	private static function createSpecialOfferList(\mysqli_result $result): array
	{
		$tags = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['special_offer_id']] = self::createSpecialOfferEntity($row);
		}

		return $tags;
	}

	private static function getSpecialOfferList($where = []): \mysqli_result|bool
	{
		return SpecialOfferTable::getList(
						[
							'special_offer_id' => 'id',
							'special_offer_title' => 'title',
							'special_offer_description' => 'description',
						],
			conditions: $where
		);
	}

	private static function getIds(\mysqli_result $result): array
	{
		$ids = [];
		while ($row = $result->fetch_assoc())
		{
			$ids[] = $row['special_offer_id'];
		}

		return $ids;
	}
}
