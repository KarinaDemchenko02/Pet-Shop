<?php

namespace Up\Dto\User;

use Up\Dto\Dto;

class UserChangeDto implements Dto
{
	public function __construct(
		public readonly int    $id,
		public readonly string $name,
		public readonly string $surname,
		public readonly string $email,
		public readonly string $phoneNumber,
		public readonly string $password,
	)
	{
	}
}
