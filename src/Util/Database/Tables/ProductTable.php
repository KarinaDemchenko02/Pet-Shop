<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\BooleanField;
use Up\Util\Database\Fields\FloatField;
use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\OneToMany;
use Up\Util\Database\Fields\Reference;
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
			new OneToMany('Image', new ImageTable, 'product'),
			new OneToMany('Tag', new ProductTagTable, 'product')
		];
	}

	public static function getTableName(): string
	{
		return 'up_item';
	}
}