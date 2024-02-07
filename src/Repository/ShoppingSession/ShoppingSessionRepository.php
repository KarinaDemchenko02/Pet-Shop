<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ShoppingSession;
use Up\Repository\Repository;

interface ShoppingSessionRepository extends Repository
{
	public static function getById(int $id): ShoppingSession;
}