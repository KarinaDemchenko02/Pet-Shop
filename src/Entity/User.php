<?php

namespace Up\Entity;

class User implements Entity
{
	public readonly int $id;
	public readonly ?string $name;
	public readonly ?string $phoneNumber;
	public readonly ?string $email;
	public readonly ?string $password;
	public readonly ?string $role;
	public readonly ?bool $isActive;

	public function __construct(
		int    $id,
		?string $name,
		?string $phoneNumber,
		?string $email,
		?string $password,
		?string $role,
		?bool $isActive
	)
	{
		$this->id = $id;
		$this->name = $name;
		$this->phoneNumber = $phoneNumber;
		$this->email = $email;
		$this->password = $password;
		$this->role = $role;
		$this->isActive = $isActive;
	}

}
