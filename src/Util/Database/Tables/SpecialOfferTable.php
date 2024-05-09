<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class SpecialOfferTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('title', false, false),
			new StringField('description', false, false),
			new StringField('start_date', false, false),
			new StringField('end_date', false, false),
			new Reflection('product_special_offer', new ProductSpecialOfferTable(), 'special_offer')
		];
	}

	public static function getTableName(): string
	{
		return 'up_special_offer';
	}
}