<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class ImageTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('path', false, false, false),
			new IntegerField('product_id', false, false),
			new Reference('product', new ProductTable, 'this.product_id=ref.id')
		];
	}

	public static function getTableName(): string
	{
		return 'up_image';
	}
}