<?php

namespace Up\Auth;


use Up\Dto\UserAddingDto;
use Up\Exceptions\Service\UserService\UserAdding;
use Up\Exceptions\Service\UserService\UserNotFound;
use Up\Service\UserService\UserService;

class Auth
{
	private array $errors = [];
	public function verifyUser(string $email, string $password): bool
	{
		try
		{
			$user = UserService::getUserByEmail($email);
			if (password_verify(trim($password), $user->password))
			{
				return true;
			}

			$this->errors[] = 'Invalid password';
			return false;
		}
		catch (UserNotFound $exception)
		{
			$this->errors[] = $exception->getMessage();
			return false;
		}
	}
	public function registerUser(string $name, string $surname, string $phoneNumber,string $email, string $password, string $roleId = 'Пользователь'): bool
	{
		if(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email))
		{
			$this->errors[] = "Invalid email";
		}
		if (!preg_match("/^[a-zA-Z]{1,30}+$/", $name) || !preg_match("/^[a-zA-Z]{1,30}+$/", $surname))
		{
			$this->errors[] = "The first or last name was entered incorrectly";
		}
		if (!preg_match("/^\+[0-9]+$/", $phoneNumber))
		{
			$this->errors[] = "Phone number was entered incorrectly";
		}
		if (!preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password))
		{
			$this->errors[] = "Пароль состоит минимум из 8 символов и может содержать только строчные и прописные латинские буквы, цифры, спецсимволы";
		}

		if (!empty($this->errors))
		{
			return false;
		}

		$password = password_hash($password, PASSWORD_DEFAULT);
		$userAddingDto = new UserAddingDto($name, $email, $password, $phoneNumber, $roleId);
		try
		{
			UserService::addUser($userAddingDto);
			return true;
		}
		catch (UserAdding)
		{
			$this->errors[] = "User with this email already exists";
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
