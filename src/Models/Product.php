<?php

namespace Up\Models;

class Product
{

	private int $id;
	private string $title;
	private string $description;
	private float $price;
	private array $tags;
	private bool $isActive;
	private int $addedAt;
	private int $editedAt;

	public function __construct(
		int    $id,
		string $title,
		string $description,
		float  $price,
		array  $tags,
		bool   $isActive,
		int    $addedAt,
		int    $editedAt
	)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->price = $price;
		$this->tags = $tags;
		$this->isActive = $isActive;
		$this->addedAt = $addedAt;
		$this->editedAt = $editedAt;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getPrice(): float
	{
		return $this->price;
	}

	public function getTags(): array
	{
		return $this->tags;
	}

	public function isActive(): bool
	{
		return $this->isActive;
	}

	public function getAddedAt(): int
	{
		return $this->addedAt;
	}

	public function getEditedAt(): int
	{
		return $this->editedAt;
	}
}