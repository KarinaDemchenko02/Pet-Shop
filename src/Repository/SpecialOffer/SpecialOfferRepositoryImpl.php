<?php

namespace Up\Repository\SpecialOffer;

use Up\Entity\Entity;
use Up\Entity\SpecialOffer;
use Up\Entity\SpecialOfferPreviewProducts;
use Up\Entity\Tag;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Repository\SpecialOffer\SpecialOfferRepository;
use Up\Util\Database\Query;

class SpecialOfferRepositoryImpl implements SpecialOfferRepository
{
	public static function getAll(): array
	{
		$query = Query::getInstance();
		$sql = "select * from up_special_offer;";

		$result = $query->getQueryResult($sql);

		$specialOffer = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$specialOffer[$row['id']] = new SpecialOffer($row['id'], $row['title'], $row['description']);
		}

		return $specialOffer;
	}

	public static function getById(int $id): SpecialOffer
	{
		$query = Query::getInstance();
		$sql = "select * from up_special_offer where id = {$id};";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new SpecialOffer($row['id'], $row['title'], $row['description']);
	}

	public static function add(string $title, string $description): bool
	{
		$query = Query::getInstance();
		$sql = "INSERT INTO up_special_offer (title, description) VALUES ('{$title}', '{$description}');";

		$result = $query->getQueryResult($sql);

		return true;
	}

	public static function getPreviewProducts()
	{
		$query = Query::getInstance();
		$sql = "select * from up_special_offer;";

		$result = $query->getQueryResult($sql);

		$specialOfferPreviewProducts = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$specialOffer = new SpecialOffer($row['id'], $row['title'], $row['description']);
			$specialOfferPreviewProducts[$row['id']] = new SpecialOfferPreviewProducts(
				$specialOffer,
				ProductRepositoryImpl::getLimitedProductsBySpecialOffer(
					$specialOffer->id
				)
			);
		}

		return $specialOfferPreviewProducts;
	}
}