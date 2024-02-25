<?php

namespace Up\Dto;


use Up\Entity\Entity;
use Up\Entity\Product;

class ProductDto implements Dto
{
	public readonly string $id;
	public readonly string $title;
	public readonly string $description;
	public readonly string $price;
	public readonly string $imagePath;
	public readonly array $characteristics;
	public function __construct(Product $product)
	{
		$this->id = $product->id;
		$this->title = $product->title;
		$this->description = $product->description;
		$this->price = $product->price;
		$this->imagePath = $product->imagePath;
		$this->characteristics = $product->characteristics;
	}

	/*public static function from(Entity $entity): void
	{
		// TODO: from() function
	}*/
}
