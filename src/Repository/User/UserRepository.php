<?php

namespace Up\Repository\User;

use Up\Entity\User;
use Up\Repository\Repository;

interface UserRepository extends Repository
{
	public static function getById(int $id): User;

	public static function getByEmail(string $email): ?User;
}
