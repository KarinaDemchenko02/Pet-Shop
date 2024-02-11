<?php

namespace Up\Repository\User;

use Up\Dto\UserAddingDto;
use Up\Entity\User;
use Up\Exceptions\Service\UserService\UserAdding;
use Up\Util\Database\QueryResult;

class UserRepositoryImpl implements UserRepository
{

	public static function getAll(): array
	{
		$sql = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id;";

		$result = QueryResult::getQueryResult($sql);

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
		$sql = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id
				where up_users.id = {$id};";

		$result = QueryResult::getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		$user = new User(
			$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
		);

		return $user;
	}

	public static function getByEmail(string $email): ?User
	{

		$sql = "select up_users.id, up_users.email, up_users.password, up_role.title as role, up_users.tel, up_users.name
				from up_users inner join up_role on up_users.role_id = up_role.id
				where up_users.email = '{$email}'";

		$result = QueryResult::getQueryResult($sql);

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
		$sql = "select id from up_role where title = '{$user->roleId}';";

		$result = QueryResult::getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);
		if (is_null($row))
		{
			throw new \RuntimeException('This role was not found');
		}
		$roleId = $row['id'];


		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		$escapedUserName = mysqli_real_escape_string($connection, $user->name);
		$escapedUserPassword = mysqli_real_escape_string($connection, $user->password);
		try
		{
			mysqli_begin_transaction($connection);
			$sql = "INSERT INTO up_users (email, password, role_id, tel, name) 
				VALUES ('{$user->email}', '{$escapedUserPassword}', {$roleId}, '{$user->phoneNumber}', '{$escapedUserName}');";

			QueryResult::getQueryResult($sql);
			mysqli_commit($connection);
		}
		catch (\Throwable $e)
		{
			mysqli_rollback($connection);
			throw new UserAdding('Failed to add a user');
		}
	}
}
