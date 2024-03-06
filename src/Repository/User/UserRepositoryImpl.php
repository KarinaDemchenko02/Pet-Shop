<?php

namespace Up\Repository\User;

use Up\Dto\User\UserAddingDto;
use Up\Dto\User\UserAdminChangeDto;
use Up\Dto\User\UserChangeDto;
use Up\Entity\User;
use Up\Exceptions\User\UserAdding;
use Up\Exceptions\User\UserNotDisabled;
use Up\Exceptions\User\UserNotFound;
use Up\Exceptions\User\UserNotRestored;
use Up\Util\Database\Orm;
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
	 */
	public static function add(UserAddingDto $user): void
	{
		$result = UserTable::add(
			[
				'name' => $user->name,
				'surname' => $user->surname,
				'email' => $user->email,
				'password' => $user->password,
				'role_id' => $user->roleId,
				'tel' => $user->phoneNumber,
			]
		);
		if ($result === 0)
		{
			throw new UserAdding('Failed to add a user');
		}
	}

	/**
	 * @throws UserNotDisabled
	 */
	public static function disable($id): void
	{
		$result = UserTable::update(['is_active' => 0], ['AND', ['=id' => $id]]);
		if ($result === 0)
		{
			throw new UserNotDisabled('User is not disabled');
		}
	}

	/**
	 * @throws UserNotRestored
	 */
	public static function restore($id): void
	{
		$result = UserTable::update(['is_active' => 1], ['AND', ['=id' => $id]]);
		if ($result === 0)
		{
			throw new UserNotRestored('User is not disabled');
		}
	}

	/**
	 * @throws UserNotFound
	 */
	public static function change(UserChangeDto $user): void
	{
		$result = UserTable::update(
			[
				'name' => $user->name,
				'surname' => $user->surname,
				'email' => $user->email,
				'tel' => $user->phoneNumber,
				'password' => $user->password,
			], ['AND', ['=id' => $user->id]]
		);
		if ($result === 0)
		{
			throw new UserNotFound();
		}
	}

	/**
	 * @throws UserNotFound
	 */
	public static function adminChange(UserAdminChangeDto $user): void
	{
		$result = UserTable::update(
			[
				'role_id' => $user->roleId,
				'name' => $user->name,
				'surname' => $user->surname,
				'email' => $user->email,
				'tel' => $user->phoneNumber,
				'password' => $user->password,
			], ['AND', ['=id' => $user->id]]
		);
		if ($result === 0)
		{
			throw new UserNotFound();
		}
	}

	public static function getAllForAdmin(int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$result = UserTable::getList(['id'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}

		return self::createUserList(self::getUserList(['AND', ['in=id' => $ids]]));
	}

	public static function createUserEntity(array $row): User
	{
		return new User(
			$row['user_id'] ?? null,
			$row['user_name'] ?? null,
			$row['user_surname'] ?? null,
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
							'user_surname' => 'surname',
							'tel',
							'email',
							'password',
							'user_is_active' => 'is_active',
							'role' => ['user_role' => 'title'],
						],
			conditions: $where
		);
	}

	private static function getIds(\mysqli_result $result): array
	{
		$ids = [];
		while ($row = $result->fetch_assoc())
		{
			$ids[] = $row['id'];
		}

		return $ids;
	}
}
