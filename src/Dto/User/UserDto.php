<?php

namespace Up\Dto\User;

use Up\Dto\Dto;
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
	public readonly ?string $surname;
	public function __construct(User $user)
	{
		$this->id = $user->id;
		$this->name = $user->name;
		$this->surname = $user->surname;
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
