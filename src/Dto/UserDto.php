<?php

namespace Up\Dto;

use Up\Entity\Entity;
use Up\Entity\User;

class UserDto implements Dto
{
	public readonly string $id;
	public readonly string $email;
	public readonly string $password;
	public readonly string $roleTitle;
	public readonly string $phoneNumber;
	public readonly string $name;
	public function __construct(User $user)
	{
		$this->id = $user->id;
		$this->email = $user->email;
		$this->password = $user->password;
		$this->roleTitle = $user->role;
		$this->phoneNumber = $user->phoneNumber;
	}
	public static function from(Entity $entity): void
	{
		// TODO: Implement from() method.
	}
}
