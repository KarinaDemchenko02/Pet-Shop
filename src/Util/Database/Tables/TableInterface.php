<?php

namespace Up\Util\Database\Tables;

interface TableInterface
{
	public static function add(array $data): int;
	public static function update(array $data, array $condition): int;


	public static function getMap():array;
	public static function getTableName(): string;

}