<?php

namespace Up\Service\UserService;

use Up\Dto\UserAddingDto;
use Up\Dto\UserDto;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotFound;
use Up\Repository\User\UserRepositoryImpl;

class UserService
{
	/**
	 * @throws UserNotFound
	 */
	public static function getUserByEmail(string $email): UserDto
	{
		$user = UserRepositoryImpl::getByEmail($email);
		if (is_null($user))
		{
			throw new UserNotFound('Пользователь с этим email не существует');
		}
		return new UserDto($user);
	}

	public static function getUserById(int $id): UserDto
	{
		$user = UserRepositoryImpl::getById($id);

		return new UserDto($user);
	}

	/**
	 * @throws UserAdding
	 */
	public static function addUser(UserAddingDto $userAddingDto): void
	{
		UserRepositoryImpl::add($userAddingDto);
	}

	public static function changeUser($id, $name, $email, $phoneNumber, $password): void
	{
		if (empty($password))
		{
			$password =  self::getUserById($id)->password;
		}
		else
		{
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		UserRepositoryImpl::change($id, $name, $email, $phoneNumber, $password);
	}
}
