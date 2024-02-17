<?php

namespace Up\Entity;

class Product implements Entity
{
	public readonly int $id;
	public readonly string $title;
	public readonly string $description;
	public readonly float $price;
	private array $tags;
	public readonly bool $isActive;
	public readonly int $addedAt;
	public readonly int $editedAt;
	public readonly string $imagePath;

	public function __construct(
		int    $id,
		string $title,
		string $description,
		float  $price,
		array  $tag,
		bool   $isActive,
		string $addedAt,
		string $editedAt,
		string $imagePath
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
		$this->imagePath = $imagePath;
	}

	public function addTag(Tag $tag)
	{
		if (!in_array($tag, $this->tags, true))
		{
			$this->tags[] = $tag;
		}
	}
	// public function addImage(Image $image)
	// {
	// 	if (!in_array($image, $this->images, true))
	// 	{
	// 		$this->images[]=$image;
	// 	}
	// }
	public function getTags(): array
	{
		return $this->tags;
	}

	// public function getImages(): array
	// {
	// 	return $this->images;
	// }
}
