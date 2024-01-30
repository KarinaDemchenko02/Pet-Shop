<?php

namespace Up\Models;

class User
{
	private int $id;
	private string $name;
	private string $phoneNumber;
	private string $email;
	private string $password;
	private string $role;

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

	public function getId(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPhoneNumber(): string
	{
		return $this->phoneNumber;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function getRole(): string
	{
		return $this->role;
	}

}