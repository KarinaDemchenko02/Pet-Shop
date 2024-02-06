<?php

namespace Up\Repository\Product;

use Up\Entity\Product;
use Up\Repository\Repository;

interface ProductRepository extends Repository
{
	public static function getById(int $id): Product;
	public static function getByTitle(string $title): array;
	public static function getByTags(array $tags): array;
}
