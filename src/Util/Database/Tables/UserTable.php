<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\BooleanField;
use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
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
			new IntegerField('role_id', false, false),
			new Reference('role', new RoleTable, 'this.role_id=ref.id'),
			new StringField('tel', isNullable: false),
			new StringField('name'),
			new BooleanField('is_active', false, true, true),
			new Reflection('order', new OrderTable(), 'user')
		];
	}

	public static function getTableName(): string
	{
		return 'up_users';
	}

	public static function delete(array $condition): int
	{
		throw new \RuntimeException("User cannot be deleted, only disabled");
	}
}