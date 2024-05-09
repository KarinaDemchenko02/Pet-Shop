<?php

namespace Up\Entity;

class Product implements Entity
{
	public readonly int $id;
	public readonly string $title;
	public readonly ?string $description;
	public readonly float $price;
	private array $tags;
	public readonly ?bool $isActive;
	public readonly ?int $addedAt;
	public readonly ?int $editedAt;
	public readonly ?string $imagePath;
	private array $specialOffer;
	public readonly ?int $priority;
	private array $characteristics;

	public function __construct(
		int     $id,
		?string $title,
		?string $description,
		float   $price,
		?array  $tag,
		?bool   $isActive,
		?string $addedAt,
		?string $editedAt,
		?string $imagePath,
		array   $specialOffer,
		?int    $priority,
		array   $characteristics,
	)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->price = $price;
		$this->tags = $tag;
		$this->isActive = $isActive;
		$this->addedAt = !is_null($addedAt) ? strtotime($addedAt) : null;
		$this->editedAt = !is_null($addedAt) ? strtotime($editedAt) : null;
		$this->imagePath = $imagePath;
		$this->priority = $priority;
		$this->specialOffer = $specialOffer;
		$this->characteristics = $characteristics;
	}

	public function addTag(Tag $tag)
	{
		if (!in_array($tag, $this->tags, true))
		{
			$this->tags[$tag->id] = $tag;
		}
	}

	public function addSpecialOffer(SpecialOffer $specialOffer): void
	{
		if (!isset($this->specialOffer[$specialOffer->id]))
		{
			$this->specialOffer[$specialOffer->id] = $specialOffer;
		}
	}

	public function addCharacteristic(ProductCharacteristic $productCharacteristic): void
	{
		foreach ($this->characteristics as $characteristic)
		{
			if ($characteristic->title===$productCharacteristic->title)
			{
				return;
			}
		}

		$this->characteristics[]=$productCharacteristic;
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
	public function getSpecialOffer(): ?array
	{
		return $this->specialOffer;
	}

	public function getCharacteristics(): array
	{
		return $this->characteristics;
	}
}
