<?php

namespace Up\Repository\User;

use Up\Dto\UserAddingDto;
use Up\Entity\User;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotFound;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\UserTable;

class UserRepositoryImpl implements UserRepository
{

	public static function getAll(): array
	{
		return self::createUserList(self::getUserList());
	}

	public static function getById(int $id): User
	{
		return self::createUserList(self::getUserList(['AND', ['=user_id' => $id]]))[$id];
	}

	public static function getByEmail(string $email): ?User
	{
		$user = array_values(self::createUserList(self::getUserList(['AND', ['=email' => $email]])));
		if (empty($user))
		{
			return null;
		}

		return $user[0];
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

		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		$escapedUserName = mysqli_real_escape_string($connection, $user->name);
		$escapedUserPassword = mysqli_real_escape_string($connection, $user->password);
		try
		{
			mysqli_begin_transaction($connection);
			$sql = "INSERT INTO up_users (email, password, role_id, tel, name) 
				VALUES ('{$user->email}', '{$escapedUserPassword}', {$roleId}, '{$user->phoneNumber}', '{$escapedUserName}');";

			$query->getQueryResult($sql);
			mysqli_commit($connection);
		}
		catch (\Throwable $e)
		{
			mysqli_rollback($connection);
			throw new UserAdding('Failed to add a user');
		}
	}

	/**
	 * @throws UserNotFound
	 */
	public static function change($id, $name, $email, $phoneNumber, $password): void
	{
		$query = Query::getInstance();

		$escapedName = $query->escape($name);
		$escapedEmail = $query->escape($email);
		$escapedPhoneNumber = $query->escape($phoneNumber);
		$escapedPassword = $query->escape($password);

		try
		{
			$query->begin();
			$changeUsersSQL = "UPDATE up_users SET name='{$escapedName}', email='{$escapedEmail}', tel= '{$escapedPhoneNumber}', password = '{$escapedPassword}' where id = {$id}";
			$query->getQueryResult($changeUsersSQL);
			if ($query->affectedRows() === 0)
			{
				throw new UserNotFound();
			}
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw $e;
		}
	}

	public static function getColumn(): array
	{
		$query = Query::getInstance();
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_NAME = 'up_users'";
		$result = $query->getQueryResult($sql);
		$columns = [];
		while ($column = mysqli_fetch_assoc($result))
		{
			$columns[] = $column;
		}

		return $columns;
	}

	public static function createUserEntity(array $row): User
	{
		return new User(
			$row['user_id'],
			$row['user_name'] ?? null,
			$row['tel'] ?? null,
			$row['email'] ?? null,
			$row['password'] ?? null,
			$row['user_role'] ?? null,
			$row['user_is_active'] ?? null,
		);
	}

	private static function createUserList(\mysqli_result $result): array
	{
		$users = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$users[$row['user_id']] = self::createUserEntity($row);
		}

		return $users;
	}

	private static function getUserList($where = []): \mysqli_result|bool
	{
		return UserTable::getList(
			[
				'user_id' => 'id',
				'user_name' => 'name',
				'tel',
				'email',
				'password',
				'user_is_active' => 'is_active',
			],
			['role' => ['user_role' => 'title']],
			conditions: $where
		);
	}
}
