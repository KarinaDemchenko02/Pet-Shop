<?php

namespace Up\Repository\ShoppingSession;

use Up\Entity\ShoppingSession;
use Up\Repository\Repository;

interface ShoppingSessionRepository extends Repository
{
	public static function getById(int $id): ShoppingSession;

	public static function getByUser(int $id): ShoppingSession;

	public static function add($userId, array $productsQuantities);

	public static function change(ShoppingSession $shoppingSession);
}