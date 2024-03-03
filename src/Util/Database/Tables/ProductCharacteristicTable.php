<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class ProductCharacteristicTable extends Table
{

	public static function getMap(): array
	{
		return [
			new Reference('product', new ProductTable(), "this.product_id=ref.id", "INNER"),
			new Reference('characteristic', new CharacteristicTable(), 'this.characteristic_id=ref.id', "INNER"),
			new StringField('value', isNullable: false),
		];
	}

	public static function getTableName(): string
	{
		return "up_product_characteristic";
	}
}