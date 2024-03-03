<?php

namespace Up\Repository\User;

use Up\Dto\UserAddingDto;
use Up\Entity\User;
use Up\Exceptions\Admin\Tag\TagNotChanged;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotFound;
use Up\Util\Database\Query;

class UserRepositoryImpl implements UserRepository
{

	public static function getAll(): array
	{
		$query = Query::getInstance();
		$sql = "select up_users.id, email, surname, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id;";

		$result = $query->getQueryResult($sql);

		$users = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$users[$row['id']] = new User(
				$row['id'], $row['name'], $row['surname'], $row['tel'], $row['email'], $row['password'], $row['role']
			);

		}

		return $users;

	}

	public static function getById(int $id): User
	{
		$query = Query::getInstance();
		$sql = "select up_users.id, email, password, up_role.title as role, tel, up_users.name, up_users.surname
				from up_users inner join up_role on up_users.role_id = up_role.id
				where up_users.id = {$id};";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new User(
			$row['id'], $row['name'], $row['surname'], $row['tel'], $row['email'], $row['password'], $row['role']
		);
	}

	public static function getByEmail(string $email): ?User
	{
		$query = Query::getInstance();
		$sql = "select up_users.id, up_users.email, up_users.password, up_role.title as role, up_users.tel, up_users.name, up_users.surname
				from up_users inner join up_role on up_users.role_id = up_role.id
				where up_users.email = '{$email}'";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		if (is_null($row))
		{
			return null;
		}

		return new User(
			$row['id'], $row['name'], $row['surname'], $row['tel'], $row['email'], $row['password'], $row['role']
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
			$query->begin();
			$sql = "INSERT INTO up_users (email, password, role_id, tel, name) 
				VALUES ('{$user->email}', '{$escapedUserPassword}', {$roleId}, '{$user->phoneNumber}', '{$escapedUserName}');";

			$query->getQueryResult($sql);
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw new UserAdding('Failed to add a user');
		}
	}

	/**
	 * @throws UserNotFound
	 */
	public static function change($id, $name, $surname, $email, $phoneNumber, $password): void
	{
		$query = Query::getInstance();

		$escapedName = $query->escape($name);
		$escapedSurname = $query->escape($surname);
		$escapedEmail = $query->escape($email);
		$escapedPhoneNumber = $query->escape($phoneNumber);
		$escapedPassword = $query->escape($password);

		try
		{
			$query->begin();
			$changeUsersSQL = "UPDATE up_users SET name='{$escapedName}', surname='{$escapedSurname}' ,email='{$escapedEmail}', tel= '{$escapedPhoneNumber}', password = '{$escapedPassword}' where id = {$id}";
			$query->getQueryResult($changeUsersSQL);
			if (Query::affectedRows() === 0)
			{
				throw new UserNotFound();
			}
			$query->commit();
		}
		catch (\Throwable $e)
		{
			$query->rollback();
			throw new UserNotFound();
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
}
