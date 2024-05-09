<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class RoleTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('title', isNullable: false),
			new Reflection('user', new UserTable, 'role')
		];
	}

	public static function getTableName(): string
	{
		return 'up_role';
	}
}