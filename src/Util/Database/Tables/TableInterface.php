<?php

namespace Up\Util\Database\Tables;

interface TableInterface
{
	public static function add(array $data): int;
	public static function update(): int;
	public static function getMap():array;
	public static function getTableName(): string;

	public static function getAll(): \mysqli_result;

}