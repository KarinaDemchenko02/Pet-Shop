<?php

namespace Up\Model;

class Tag
{
	public function __construct(
		readonly string $id,
		readonly string $title
	){}
}
