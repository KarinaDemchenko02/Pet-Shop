<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Orm;

class TagTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('name', false, false),
			new Reflection('product', new ProductTagTable, 'tag')
		];
	}

	public static function getTableName(): string
	{
		return 'up_tags';
	}

	public static function getById($id): \mysqli_result
	{
		$tableName = self::getTableName();

		return Orm::getInstance()->select(static::getTableName(), static::getAllColumns(), "$tableName.id=$id");
	}
}