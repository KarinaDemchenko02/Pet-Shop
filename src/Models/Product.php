<?php

namespace Up\Models;

class Product
{

	readonly int $id;
	readonly string $title;
	readonly string $description;
	readonly float $price;
	readonly array $tags;
	readonly bool $isActive;
	readonly int $addedAt;
	readonly int $editedAt;

	public function __construct(
		int    $id,
		string $title,
		string $description,
		float  $price,
		array  $tag,
		bool   $isActive,
		string $addedAt,
		string $editedAt
	)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->price = $price;
		$this->tags = $tag;
		$this->isActive = $isActive;
		$this->addedAt = strtotime($addedAt);
		$this->editedAt = strtotime($editedAt);
	}

	// public function addTag(Tag $tag): void
	// {
	// 	$this->tags[]=$tag;
	// }

}