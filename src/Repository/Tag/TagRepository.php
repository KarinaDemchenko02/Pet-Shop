<?php

namespace Up\Repository\Tag;

use Up\Models\Tag;
use Up\Repository\Repository;

interface TagRepository extends Repository
{
	public static function getById(int $id): Tag;
}