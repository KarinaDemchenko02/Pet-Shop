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
			new StringField('title', false, false),
			new Reflection('product', new ProductTagTable(), 'tag'),
		];
	}

	public static function getTableName(): string
	{
		return 'up_tags';
	}
}