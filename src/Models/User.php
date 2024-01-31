<?php

namespace Up\Models;

class User
{
	readonly int $id;
	readonly string $name;
	readonly string $phoneNumber;
	readonly string $email;
	readonly string $password;
	readonly string $role;

	public function __construct(
		int    $id,
		string $name,
		string $phoneNumber,
		string $email,
		string $password,
		string $role
	)
	{
		var_dump($name);
		$this->id = $id;
		$this->name = $name;
		$this->phoneNumber = $phoneNumber;
		$this->email = $email;
		$this->password = $password;
		$this->role = $role;
	}

}