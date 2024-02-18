<?php

namespace Up\Auth;


use Up\Dto\UserAddingDto;
use Up\Dto\UserDto;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotFound;
use Up\Service\UserService\UserService;

class Auth
{
	private array $errors = [];
	public function verifyUser(UserDto $userDto, string $password): bool
	{
		try
		{
			if (password_verify(trim($password), $userDto->password))
			{
				return true;
			}
			$this->errors[] = 'Invalid password';
		}
		catch (UserNotFound $exception)
		{
			$this->errors[] = $exception->getMessage();
		}
		return false;
	}

	public function registerUser(UserAddingDto $user): bool
	{
		if(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $user->email))
		{
			$this->errors[] = "Неправильно введён Email";
		}
		if (!preg_match("/^[a-zA-Z]{1,30}+$/", $user->name) || !preg_match("/^[a-zA-Z]{1,30}+$/", $user->surname))
		{
			$this->errors[] = "Имя и фамилия введены некорректно";
		}
		if (!preg_match("/^\+\d+$/", $user->phoneNumber))
		{
			$this->errors[] = "Номер телефона введён некорректно";
		}
		if (!preg_match("/^(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/", $user->password))
		{
			$this->errors[] = "Пароль состоит минимум из 8 символов и может содержать только строчные и прописные латинские буквы, цифры";
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
			$user->roleTitle
		);
		try
		{
			UserService::addUser($userAddingDto);
			return true;
		}
		catch (UserAdding)
		{
			$this->errors[] = "Пользователь с этим Email уже существует";
			return false;
		}
	}

	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}
}
