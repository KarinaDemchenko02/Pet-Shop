<?php

namespace Up\Repository\Order;

use Up\Entity\Order;
use Up\Repository\Repository;

interface OrderRepository extends Repository
{
	public static function getById(int $id): Order;
}
