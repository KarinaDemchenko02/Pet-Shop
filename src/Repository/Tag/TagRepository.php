<?php

namespace Up\Repository\Tag;

use Up\Entity\Tag;
use Up\Repository\Repository;

interface TagRepository extends Repository
{
	public static function getById(int $id): Tag;

	public static function add(string $title): void;
}
