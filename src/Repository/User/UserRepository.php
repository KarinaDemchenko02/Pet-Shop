<?php

namespace Up\Repository\User;

use Up\Models\User;
use Up\Repository\Repository;

interface UserRepository extends Repository
{
	public static function getById(int $id): User;
}