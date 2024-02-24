<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reflection;
use Up\Util\Database\Fields\StringField;
use Up\Util\Database\Tables\Table;

class SpecialOfferTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('id', true, false, true),
			new StringField('title', false, false),
			new StringField('description', false, false),
			new Reflection('product', new ProductSpecialOfferTable(), 'specialOffer')
		];
	}

	public static function getTableName(): string
	{
		return 'up_special_offer';
	}
}

/*create table up_special_offer
(
	id          int auto_increment
		primary key,
	title       varchar(45)  not null,
	description varchar(255) not null
);*/