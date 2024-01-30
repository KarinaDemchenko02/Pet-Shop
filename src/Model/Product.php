<?php

namespace Up\Model;

class Product
{
	public function __construct(
		readonly string $id,
		readonly string $title,
		readonly string $description,
		readonly float  $price,
		readonly string $status,
		readonly array $tagsId,
	){}
}
