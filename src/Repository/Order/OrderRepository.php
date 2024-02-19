<?php

namespace Up\Repository\Order;

use Up\Dto\Order\OrderAddingDto;
use Up\Entity\Order;
use Up\Repository\Repository;

interface OrderRepository extends Repository
{
	public static function getById(int $id): Order;

	public static function add(OrderAddingDto $order): void;
}
