<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Tables\Table;

class ShoppingSessionItemTable extends Table
{

	public static function getMap(): array
	{
		return [
			new Reference('product', new ProductTable, ['this.item_id=ref.id']),
			new Reference('shoppingSession', new ShoppingSessionTable, ['this.shopping_session_id=ref.id']),
			new IntegerField('quantities'),
		];
	}

	public static function getTableName(): string
	{
		return 'up_shopping_session_item';
	}
}

/*item_id             int not null,
	shopping_session_id int not null,
	quantities          int null,*/