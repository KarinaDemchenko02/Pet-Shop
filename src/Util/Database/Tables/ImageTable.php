<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class ImageTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('path', false, false, false),
		];
	}

	public static function getTableName(): string
	{
		return 'up_image';
	}

	public static function update(): int
	{
		// TODO: Implement update() method.
	}
}