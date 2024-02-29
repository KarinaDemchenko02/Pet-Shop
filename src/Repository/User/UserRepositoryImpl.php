<?php

namespace Up\Repository\User;

use Up\Dto\UserAddingDto;
use Up\Entity\User;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotFound;
use Up\Util\Database\Orm;
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
		UserTable::add(
			[
				'name' => $user->name,
				'surname' => $user->surname,
				'email' => $user->email,
				'password' => $user->password,
				'role_id' => $user->roleId,
				'tel' => $user->phoneNumber,
			]
		);
		throw new UserAdding('Failed to add a user');
	}

	/**
	 * @throws UserNotFound
	 */
	public static function change($id, $name, $email, $phoneNumber, $password): void
	{
		$orm = Orm::getInstance();
		UserTable::update(['name' => $name, 'email' => $email, 'tel' => $phoneNumber, 'password' => $password],
						  ['AND', ['id' => $id]]);

		if ($orm->affectedRows() === 0)
		{
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

	public static function createUserEntity(array $row): User
	{
		return new User(
			$row['user_id'] ?? null,
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
							'role' => ['user_role' => 'title']],
			conditions: $where
		);
	}
}
