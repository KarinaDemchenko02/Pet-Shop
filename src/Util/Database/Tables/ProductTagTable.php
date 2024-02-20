<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\ReferenceField;
use Up\Util\Database\Tables\Table;

class ProductTagTable extends Table
{

	public static function getMap(): array
	{
		return [
			new ReferenceField(new TagTable(), ['this.id_tag=ref.id'], 'inner', true, false),
			new ReferenceField(new ProductTable(), ['this.id_item=ref.id'], 'inner', true, false)
		];
	}

	public static function getTableName(): string
	{
		return 'up_item_tag';
	}

	public static function update(): int
	{
		// TODO: Implement update() method.
	}
}