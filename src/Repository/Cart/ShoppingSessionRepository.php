<?php

namespace Up\Repository\Cart;

use Up\Entity\Order;
use Up\Repository\Repository;

interface ShoppingSessionRepository extends Repository
{
	public static function getById(int $id): Order;
}