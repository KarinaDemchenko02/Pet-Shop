<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
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
			new IntegerField('user_id', false, false),
			new StringField('delivery_address', isNullable: false),
			new Reference('status', new StatusTable, 'this.status_id=ref.id', isNullable: false),
			new IntegerField('status_id', false, false),
			new StringField('created_at', isDefaultExists: true),
			new StringField('edited_at', isDefaultExists: true),
			new StringField('name', isNullable: true),
			new StringField('surname', isNullable: true),
			new Reflection('order_product', new OrderProductTable(), 'order')
		];
	}

	public static function getTableName(): string
	{
		return 'up_order';
	}

	public static function delete(array $condition): int
	{
		throw new \RuntimeException("The order cannot be deleted, only the status changed to cancelled");
	}

	public static function getColumnsName(): array
	{
		return ['ID', 'ID Пользователя', 'Адресс доставки', 'Статус заказа', 'Дата создания', 'Дата изменения', 'Имя', 'Фамилия'];
	}
}