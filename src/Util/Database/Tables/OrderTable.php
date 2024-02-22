<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\ManyToMany;
use Up\Util\Database\Fields\OneToMany;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class OrderTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new Reference('user', new UserTable, 'this.user_id=ref.id', isNullable: true),
			new StringField('delivery_address', isNullable: false),
			new Reference('status', new StatusTable, 'this.status_id=ref.id', isNullable: false),
			new StringField('created_at', isDefaultExists: true),
			new StringField('edited_at', isDefaultExists: true),
			new StringField('name', isNullable: true),
			new StringField('surname', isNullable: true),
			new ManyToMany('product', new OrderProductTable(), 'order')
		];
	}

	public static function getTableName(): string
	{
		return 'up_order';
	}

}