<?php

namespace Up\Repository\Order;

use Up\Models\Order;
use Up\Repository\Repository;

interface OrderRepository extends Repository
{
	public static function getById(int $id): Order;
}