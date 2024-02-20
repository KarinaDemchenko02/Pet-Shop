<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\BooleanField;
use Up\Util\Database\Fields\FloatField;
use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\ReferenceField;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class ProductTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('name', false, false),
			new StringField('description'),
			new FloatField('price', isNullable: false),
			new StringField('added_at', isNullable: false, isDefaultExists: true),
			new StringField('edited_at', isNullable: false, isDefaultExists: true),
			new BooleanField('is_active', isNullable: false, isDefaultExists: true),
			new ReferenceField(new ImageTable, ['this.id=ref.item_id'], 'INNER')
		];
	}

	public static function getTableName(): string
	{
		return 'up_item';
	}

	public static function update(): int
	{
		// TODO: Implement update() method.
	}

	public static function delete(): int
	{
		// TODO: Implement delete() method.
	}
}