<?php

namespace Up\Dto;


use Up\Entity\Entity;
use Up\Entity\Product;

class ProductDto implements Dto
{
	public readonly string $id;
	public readonly string $title;
	public readonly string $description;
	public function __construct(Product $product)
	{
		$this->id = $product->id;
		$this->title = $product->title;
		$this->description = $product->description;
	}

	public function from(Entity $entity): void
	{

	}
}
