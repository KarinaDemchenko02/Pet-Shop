<?php

namespace Up\Dto;

class TokenDto implements Dto
{
	public function __construct(
		readonly int $uid,
		readonly string $jti,
		readonly int $exp,
	)
	{
	}
}
