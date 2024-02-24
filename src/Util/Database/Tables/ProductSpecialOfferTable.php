<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Tables\Table;

class ProductSpecialOfferTable extends Table
{

	public static function getMap(): array
	{
		return [
			new Reference('product', new ProductTable(), "this.item_id=ref.id"),
			new Reference('specialOffer', new SpecialOfferTable(), "this.special_offer_id=ref.id"),
		];
	}

	public static function getTableName(): string
	{
		return 'up_item_special_offer';
	}
}