<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\BooleanField;
use Up\Util\Database\Fields\FloatField;
use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
use Up\Util\Database\Fields\StringField;

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
			new IntegerField('priority', isDefaultExists: true),
			new Reflection('image', new ImageTable(), 'product'),
			new Reflection('tag', new ProductTagTable(), 'product'),
			new Reflection('shoppingSession', new ShoppingSessionTable(), 'product'),
			new Reflection('order', new OrderProductTable(), 'product'),
			new Reflection('specialOffer', new ProductSpecialOfferTable(), 'product'),
			new Reflection('characteristic', new CharacteristicProductTable(), 'product')
		];
	}

	public static function getTableName(): string
	{
		return 'up_item';
	}

	public static function delete(array $condition): int
	{
		throw new \RuntimeException("Product cannot be deleted, only disabled");
	}
}