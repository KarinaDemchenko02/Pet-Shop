<?php

namespace Up\Repository\User;

use Up\Dto\UserAddingDto;
use Up\Entity\User;
use Up\Exceptions\Service\UserService\UserAdding;
use Up\Util\Database\Query;

class UserRepositoryImpl implements UserRepository
{
	private const SELECT_SQL = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id ";

	public static function getAll(): array
	{
		$query = Query::getInstance();
		$result = $query->getQueryResult(self::SELECT_SQL);

		$users = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$users[$row['id']] = new User(
				$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
			);

		}

		return $users;

	}

	public static function getById(int $id): User
	{
		$query = Query::getInstance();
		$sql = self::SELECT_SQL . "where up_users.id = {$id};";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		$user = new User(
			$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
		);

		return $user;
	}

	public static function getByEmail(string $email): ?User
	{
		$query = Query::getInstance();
		$sql = self::SELECT_SQL . "where up_users.email = '{$email}'";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		if (is_null($row))
		{
			return null;
		}

		return new User(
			$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
		);
	}

	/**
	 * @throws UserAdding
	 * @throws \Exception
	 */
	public static function add(UserAddingDto $user): void
	{
		$query = Query::getInstance();
		$sql = "select id from up_role where title = '{$user->roleTitle}';";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);
		if (is_null($row))
		{
			throw new \RuntimeException('This role was not found');
		}
		$roleId = $row['id'];

		$escapedUserName = $query->escape($user->name);
		$escapedUserPassword = $query->escape($user->password);
		try
		{
			$sql = "INSERT INTO up_users (email, password, role_id, tel, name) 
				VALUES ('{$user->email}', '{$escapedUserPassword}', {$roleId}, '{$user->phoneNumber}', '{$escapedUserName}');";
			$query->getQueryResult($sql);
		}
		catch (\Throwable $e)
		{
			throw new UserAdding('Failed to add a user');
		}
	}
}
