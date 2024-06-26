<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class CharacteristicTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('title', false, false),
			new Reflection('product_characteristic', new ProductCharacteristicTable, 'characteristic')
		];
	}

	public static function getTableName(): string
	{
		return 'up_characteristic';
	}
}