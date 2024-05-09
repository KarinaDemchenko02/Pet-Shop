<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\StringField;

class TokenTable extends Table
{

	public static function getMap(): array
	{
		return [
			new StringField('jti', true, false),
			new IntegerField('user_id', false, false),
			new IntegerField('expiration', false, false),
			new StringField('finger_print', false),
			new Reference('user', new UserTable(), 'this.user_id=ref.id'),
		];
	}

	public static function getTableName(): string
	{
		return 'up_token';
	}
}



/*
 *
 * CREATE TABLE up_token
(
	jti          VARCHAR(50) NOT NULL
		PRIMARY KEY,
	user_id      INT         NOT NULL,
	expiration   INT         NOT NULL,
	finger_print VARCHAR(50) NULL,
	CONSTRAINT fk_token_user
		FOREIGN KEY (user_id) REFERENCES up_users (id)
);

*/