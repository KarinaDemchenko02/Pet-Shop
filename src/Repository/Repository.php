<?php

namespace Up\Repository;

use Up\Entity\Entity;

interface Repository
{
	public static function getAll(): array;
	public static function getById(int $id): Entity;
	// public static function add(Entity $entity): void;
}
