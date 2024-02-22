<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\OneToMany;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class ShoppingSessionTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new Reference('user', new UserTable, 'this.user_id=ref.id'),
			new StringField('created_at', isDefaultExists: true),
			new StringField('updated_at', isDefaultExists: true),
			new ManyToMany('product', new ShoppingSessionItemTable(), 'shoppingSession')
		];
	}

	public static function getTableName(): string
	{
		return 'up_shopping_session';
	}
}