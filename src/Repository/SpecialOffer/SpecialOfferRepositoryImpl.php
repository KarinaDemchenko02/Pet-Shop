<?php

namespace Up\Repository\SpecialOffer;

use Up\Entity\Entity;
use Up\Entity\SpecialOffer;
use Up\Entity\Tag;
use Up\Repository\SpecialOffer\SpecialOfferRepository;
use Up\Util\Database\QueryResult;

class SpecialOfferRepositoryImpl implements SpecialOfferRepository
{

	public static function getAll(): array
	{

		$sql = "select * from up_special_offer;";

		$result = QueryResult::getQueryResult($sql);

		$specialOffer = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$specialOffer[$row['id']] = new SpecialOffer($row['id'], $row['title'], $row['description']);
		}

		return $specialOffer;
	}

	public static function getById(int $id): SpecialOffer
	{
		$sql = "select * from up_special_offer where id = {$id};";

		$result = QueryResult::getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new SpecialOffer($row['id'], $row['title'], $row['description']);
	}

	public static function add(string $title, string $description): bool
	{
		$sql = "INSERT INTO up_special_offer (title, description) VALUES ('{$title}', '{$description}');";

		$result = QueryResult::getQueryResult($sql);

		return true;
	}
}