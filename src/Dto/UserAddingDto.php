<?php

namespace Up\Dto;

use Up\Entity\Entity;
use Up\Entity\User;

class UserAddingDto implements Dto
{
	/*public readonly string $name;
	public readonly string $email;
	public readonly string $password;
	public readonly string $phoneNumber;
	public readonly string $roleId;*/

	public function __construct(
		public readonly string $name,
		public readonly string $email,
		public readonly string $password,
		public readonly string $phoneNumber,
		public readonly string $roleId,
	){}

	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
