<?php

namespace Up\Entity;

class SpecialOfferPreviewProducts implements Entity
{
	public readonly SpecialOffer $specialOffer;
	private array $products;

	public function __construct(SpecialOffer $specialOffer, array $products)
	{
		$this->specialOffer = $specialOffer;
		$this->products = $products;
	}

	public function addProduct(Product $product)
	{
		if (!in_array($product, $this->products, true) && count($this->products)<\Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PREVIEW'))
		{
			$this->products[$product->id] = $product;
		}
	}

	public function getProducts(): array
	{
		return $this->products;
	}
}