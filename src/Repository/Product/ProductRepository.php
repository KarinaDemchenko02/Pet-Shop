<?php

namespace Up\Repository\Product;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Entity\Product;
use Up\Entity\Tag;
use Up\Repository\Repository;

interface ProductRepository extends Repository
{
	public static function getById(int $id): Product;

	public static function getByTitle(string $title): array;

	public static function getByTags(array $tags): array;
	

	public static function add(ProductAddingDto $productAddingDto): int;

	public static function disable($id): void;

	public static function change(ProductChangeDto $productChangeDto): void;

	public static function getColumn(): array;
}
