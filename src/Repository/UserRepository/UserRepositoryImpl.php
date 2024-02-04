<?php

namespace Up\Repository\UserRepository;

use Up\Entity\User;


class UserRepositoryImpl implements UserRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Configuration::getInstance()->option('DB_HOST'),
			\Up\Util\Configuration::getInstance()->option('DB_USER'),
			\Up\Util\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id;";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \Exception(mysqli_error($connection));
		}

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
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Configuration::getInstance()->option('DB_HOST'),
			\Up\Util\Configuration::getInstance()->option('DB_USER'),
			\Up\Util\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select up_users.id, email, password, up_role.title as role, tel, name
				from up_users inner join up_role on up_users.role_id = up_role.id
				where up_users.id = {$id};";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \Exception(mysqli_error($connection));
		}

		$row = mysqli_fetch_assoc($result);

		$user = new User(
			$row['id'], $row['name'], $row['tel'], $row['email'], $row['password'], $row['role']
		);

		return $user;
	}

}
