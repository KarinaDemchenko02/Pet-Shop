<?php

namespace Up\Repository;

use Up\Repository\Repository;
use Up\Service\Database;
use Up\Models;

class User extends Repository
{

	public static function getAll(): array
	{
		$database = new Database();
		$connection = $database->getDbConnection();

		$sql = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id;";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$users = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$users[$row['id']] = new Models\User(
				$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
			);

		}

		return $users;

	}

	public static function getById(int $id): Models\User
	{
		$database = new Database();
		$connection = $database->getDbConnection();

		$sql = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id
				where up_users.id = {$id};";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$row = mysqli_fetch_assoc($result);

		$user = new Models\User(
			$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
		);

		return $user;
	}

}