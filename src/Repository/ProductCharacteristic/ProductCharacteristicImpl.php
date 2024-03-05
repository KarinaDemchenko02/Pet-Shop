<?php

namespace Up\Repository\ProductCharacteristic;

use Up\Entity\Characteristic;
use Up\Util\Database\Tables\CharacteristicTable;

class ProductCharacteristicImpl implements ProductCharacteristic
{

	public static function getAll(): array
	{
		return self::createCharacteristicList(self::getCharacteristicList());
	}

	public static function getById(int $id): Characteristic
	{
		return self::createCharacteristicList(self::getCharacteristicList(['AND', ['=characteristic_id' => $id]]))[$id];
	}

	public static function createCharacteristicEntity(array $row): Characteristic
	{
		return new Characteristic($row['characteristic_id'], $row['characteristic_title']);
	}

	private static function createCharacteristicList(\mysqli_result $result): array
	{
		$characteristics = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$characteristics[$row['characteristic_id']] = self::createCharacteristicEntity($row);
		}

		return $characteristics;
	}

	private static function getCharacteristicList($where = []): \mysqli_result|bool
	{
		return CharacteristicTable::getList(['characteristic_id' => 'id', 'characteristic_title' => 'title'],
			conditions:                     $where);
	}

	public static function add(string $characteristicTitle): void
	{
		CharacteristicTable::add(['title' => $characteristicTitle]);
	}
}