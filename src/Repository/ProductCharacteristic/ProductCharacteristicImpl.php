<?php

namespace Up\Repository\ProductCharacteristic;

use Up\Entity\Characteristic;
use Up\Entity;
use Up\Repository\ProductCharacteristic\ProductCharacteristic;
use Up\Util\Database\Query;

class ProductCharacteristicImpl implements ProductCharacteristic
{

	public static function getAll(): array
	{
		$query = Query::getInstance();
		$sql = "select * from up_characteristic;";

		$result = $query->getQueryResult($sql);

		$characteristics = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$characteristics[$row['id']] = new Characteristic($row['id'], $row['title']);
		}

		return $characteristics;
	}

	public static function getById(int $id): Characteristic
	{
		$query = Query::getInstance();
		$sql = "select * from up_characteristic where id = {$id};";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new Characteristic($row['id'], $row['title']);
	}

	public static function getAllByProductId(int $id): array
	{
		$query = Query::getInstance();
		$sql = "select id, title, value
				from up_characteristic
				inner join eshop.up_item_characteristic uic on up_characteristic.id = uic.characteristic_id
				where item_id = {$id};";

		$result = $query->getQueryResult($sql);
		$productCharacteristics = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$productCharacteristics[$row['id']] = new Entity\ProductCharacteristic($row['title'], $row['value']);
		}

		return $productCharacteristics;
	}
}