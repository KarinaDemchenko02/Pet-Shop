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
			new Reflection('product_tag', new ProductTagTable(), 'product'),
			new Reflection('shopping_session_product', new ShoppingSessionTable(), 'product'),
			new Reflection('order_product', new OrderProductTable(), 'product'),
			new Reflection('product_special_offer', new ProductSpecialOfferTable(), 'product'),
			new Reflection('product_characteristic', new ProductCharacteristicTable(), 'product')
		];
	}

	public static function getTableName(): string
	{
		return 'up_product';
	}

	public static function delete(array $condition): int
	{
		throw new \RuntimeException("Product cannot be deleted, only disabled");
	}

	public static function getColumnsName(): array
	{
		return ['ID', 'Название', 'Описание', 'Цена', 'Дата создания', 'Дата изменения', 'Активен', 'Приоритет'];
	}
}