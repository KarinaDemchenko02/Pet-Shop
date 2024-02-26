<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class CharacteristicProductTable extends Table
{

	public static function getMap(): array
	{
		return [
			new Reference('product', new ProductTable(), "this.item_id=ref.id"),
			new Reference('characteristic', new CharacteristicTable(), 'this.characteristic_id=ref.id'),
			new StringField('value', isNullable: false),
		];
	}

	public static function getTableName(): string
	{
		return "up_item_characteristic";
	}
}