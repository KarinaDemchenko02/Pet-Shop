<?php

namespace Up\Repository\User;

use Up\Entity\User;
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

	public static function add(User $user): bool
	{
		$sql = "select id from up_role where title = '{$user->role}';";

		$result = QueryResult::getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		$roleId = $row['id'];

		$sql = "INSERT INTO up_users (email, password, role_id, tel, name) 
				VALUES ('{$user->email}', '{$user->password}', {$roleId}, '{$user->phoneNumber}', '{$user->name}');";

		QueryResult::getQueryResult($sql);

		return true;
	}
}
