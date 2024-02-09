<?php

namespace Up\Service\UserService;

use Up\Dto\UserAddingDto;
use Up\Dto\UserDto;
use Up\Exceptions\Service\UserService\UserAdding;
use Up\Exceptions\Service\UserService\UserNotFound;
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
			throw new UserNotFound('User with this email does not exist');
		}
		return new UserDto($user);
	}

	/**
	 * @throws UserAdding
	 */
	public static function addUser(UserAddingDto $userAddingDto): void
	{
		UserRepositoryImpl::add($userAddingDto);
	}
}
