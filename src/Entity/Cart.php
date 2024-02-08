<?php

namespace Up\Entity;
/*$product = [[Product, quantities]]*/
class Cart implements Entity
{
	public function __construct(readonly array $products)
	{
	}

	public function getProductIds()
	{
		$productsIds = [];
		foreach ($this->products as $product)
		{
			$productsIds[] = $product[0];
		}
		return $productsIds;
	}

}