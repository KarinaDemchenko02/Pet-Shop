<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Tables\Table;

class ProductTagTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('tag_id', true, false),
			new IntegerField('product_id', true, false),
			new Reference('tag', new TagTable(), 'this.tag_id=ref.id'),
			new Reference('product', new ProductTable(), 'this.product_id=ref.id'),
		];
	}

	public static function getTableName(): string
	{
		return 'up_product_tag';
	}
}