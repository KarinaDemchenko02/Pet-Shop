<?php

namespace Up\Dto\Tag;

class TagChangingDto implements \Up\Dto\Dto
{
	public function __construct(
		public readonly int $id,
		public readonly string $title,
	)
	{
	}
}
