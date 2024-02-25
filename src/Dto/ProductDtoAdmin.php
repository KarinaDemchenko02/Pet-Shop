<?php

namespace Up\Dto;


use Up\Entity\Entity;
use Up\Entity\Product;

class ProductDtoAdmin implements Dto
{
	public readonly string $id;
	public readonly string $title;
	public readonly string $description;
	public readonly string $price;
	public readonly bool $isActive;
	public readonly string $imagePath;
	public readonly int $addedAt;
	public readonly int $editedAt;
	public readonly array $tags;

	public function __construct(Product $product)
	{
		$this->id = $product->id;
		$this->title = $product->title;
		$this->description = $product->description;
		$this->price = $product->price;
		$this->imagePath = $product->imagePath;
		$this->isActive = $product->isActive;
		$this->addedAt = $product->addedAt;
		$this->editedAt = $product->editedAt;
		$this->tags = $product->getTags();
	}

	public static function from(Entity $entity): void
	{
		// TODO: from() function
	}
}
