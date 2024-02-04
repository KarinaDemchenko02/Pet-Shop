<?php

namespace Up\Dto;

use Up\Entity\Entity;
use Up\Entity\Tag;


class TagDto implements Dto
{
	public readonly string $id;
	public readonly string $title;
	public function __construct(Tag $tag)
	{
		$this->id = $tag->id;
		$this->title = $tag->title;
	}
	public function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
