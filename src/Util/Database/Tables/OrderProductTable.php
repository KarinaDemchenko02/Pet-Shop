<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\FloatField;
use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Tables\Table;

class OrderProductTable extends Table
{

	public static function getMap(): array
	{
		return [
			new Reference('order', new OrderTable(), 'this.order_id=ref.id'),
			new Reference('product', new ProductTable(), 'this.item_id=ref.id'),
			new IntegerField('item_id', false, false),
			new IntegerField('order_id', false, false),
			new IntegerField('quantities', isNullable: false),
			new FloatField('price', isNullable: false),
		];
	}

	public static function getTableName(): string
	{
		return 'up_order_item';
	}
}