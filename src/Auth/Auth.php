<?php

namespace Up\Auth;


use Up\Dto\UserAddingDto;
use Up\Dto\UserDto;
use Up\Exceptions\Auth\InvalidPassword;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotFound;
use Up\Service\UserService\UserService;

class Auth
{
	private array $errors = [];
	public function verifyUser(UserDto $userDto, string $password): bool
	{
		if (password_verify(trim($password), $userDto->password))
		{
			return true;
		}

		$this->errors[] = 'Invalid password';
		return false;
	}

	public function registerUser(UserAddingDto $user): bool
	{
		if(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $user->email))
		{
			$this->errors[] = "Invalid Email";
		}
		if (!preg_match("/^[a-zA-Z]{1,30}+$/", $user->name) || !preg_match("/^[a-zA-Z]{1,30}+$/", $user->surname))
		{
			$this->errors[] = "Invalid name or surname";
		}
		if (!preg_match("/^\+\d+$/", $user->phoneNumber))
		{
			$this->errors[] = "Invalid phone number";
		}
		if (!preg_match("/^(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/", $user->password))
		{
			$this->errors[] = 'Invalid password';//"Пароль состоит минимум из 8 символов и может содержать только строчные и прописные латинские буквы, цифры";
		}

		if (!empty($this->errors))
		{
			return false;
		}

		$password = password_hash($user->password, PASSWORD_DEFAULT);
		$userAddingDto = new UserAddingDto(
			$user->name,
			$user->surname,
			$user->email,
			$password,
			$user->phoneNumber,
			$user->roleId,
		);
		try
		{
			UserService::addUser($userAddingDto);
			return true;
		}
		catch (UserAdding)
		{
			$this->errors[] = 'This email is busy';//"Пользователь с этим Email уже существует";
			return false;
		}
	}

	/**
	 * @throws InvalidPassword
	 */
	public static function hashPassword(string $password): string
	{
		if (!preg_match("/^(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/", $password))
		{
			throw new InvalidPassword("Пароль состоит минимум из 8 символов и может содержать только строчные и прописные латинские буквы, цифры");
		}
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}
}
