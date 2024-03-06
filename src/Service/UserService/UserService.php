<?php

namespace Up\Service\UserService;

use Up\Auth\Auth;
use Up\Dto\User\UserAddingDto;
use Up\Dto\User\UserChangeDto;
use Up\Dto\User\UserDto;
use Up\Exceptions\Auth\InvalidPassword;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotDisabled;
use Up\Exceptions\User\UserNotFound;
use Up\Exceptions\User\UserNotRestored;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Database\Tables\UserTable;

class UserService
{
	/**
	 * @throws UserNotFound
	 */

	public static function getAll(): array
	{
		$users = UserRepositoryImpl::getAll();
		$usersDto = [];
		foreach ($users as $user)
		{
			$usersDto[$user->id] = new UserDto($user);
		}

		return $usersDto;
	}

	public static function getColumn(): array
	{
		return UserTable::getColumnsName();
	}

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
	 * @throws UserNotFound
	 * @throws InvalidPassword
	 */
	public static function changeUser($id, $name, $surname, $email, $phoneNumber, $password): void
	{
		if (empty($password))
		{
			$password = self::getUserById($id)->password;
		}
		else
		{
			$password = Auth::hashPassword($password);
		}
		$user = new UserChangeDto($id, $name, $surname, $email, $phoneNumber, $password);
		UserRepositoryImpl::change($user);
	}

	/**
	 * @throws UserNotDisabled
	 */
	public static function disableUser(int $id): void
	{
		UserRepositoryImpl::disable($id);
	}

	/**
	 * @throws UserNotRestored
	 */

	public static function restoreUser(int $id): void
	{
		UserRepositoryImpl::restore($id);
	}

	public static function getAllProductsForAdmin(int $page = 1): array
	{
		$users = UserRepositoryImpl::getAllForAdmin($page);
		$usersDto = [];
		foreach ($users as $user)
		{
			$usersDto[] = new UserDto($user);
		}

		return $usersDto;
	}
}
