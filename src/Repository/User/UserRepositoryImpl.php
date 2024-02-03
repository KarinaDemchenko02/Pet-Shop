<?php

namespace Up\Repository\User;

use Up\Models;

class UserRepositoryImpl implements UserRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Service\Database::getInstance(
			\Up\Service\Configuration::getInstance()->option('DB_HOST'),
			\Up\Service\Configuration::getInstance()->option('DB_USER'),
			\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Service\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

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
		$connection = \Up\Service\Database::getInstance(
			\Up\Service\Configuration::getInstance()->option('DB_HOST'),
			\Up\Service\Configuration::getInstance()->option('DB_USER'),
			\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Service\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

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