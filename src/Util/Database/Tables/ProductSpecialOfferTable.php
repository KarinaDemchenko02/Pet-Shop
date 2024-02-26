<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\IntegerField;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Tables\Table;

class ProductSpecialOfferTable extends Table
{

	public static function getMap(): array
	{
		return [
			new IntegerField('product_id', true, false, false),
			new Reference('product', new ProductTable(), "this.product_id=ref.id"),
			new IntegerField('special_offer_id', true, false, false),
			new Reference('specialOffer', new SpecialOfferTable(), "this.special_offer_id=ref.id"),
		];
	}

	public static function getTableName(): string
	{
		return 'up_product_special_offer';
	}
}