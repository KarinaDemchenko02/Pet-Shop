<?php

namespace Up\Entity;

class ShoppingSession implements Entity
{
	public function __construct(
		readonly int  $id,
		readonly int  $createdAt,
		readonly int  $editedAt,
		readonly Cart $cart
	)
	{
	}

}