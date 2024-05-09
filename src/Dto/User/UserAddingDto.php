<?php

namespace Up\Dto\User;

use Up\Dto\Dto;
use Up\Entity\Entity;

class UserAddingDto implements Dto
{
	public function __construct(
		public readonly string $name,
		public readonly string $surname,
		public readonly string $email,
		public readonly string $password,
		public readonly string $phoneNumber,
		public readonly int $roleId,
	){}

	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
