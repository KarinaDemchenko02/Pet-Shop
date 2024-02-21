<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\OneToMany;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class UserTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('email', isNullable: false),
			new StringField('password'),
			new Reference('role', new RoleTable, ['this.role_id=ref.id']),
			new StringField('tel', isNullable: false),
			new StringField('name'),
			new OneToMany('order', new OrderTable(), 'user')
		];
	}

	public static function getTableName(): string
	{
		return 'up_users';
	}
}