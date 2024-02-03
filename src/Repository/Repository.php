<?php

namespace Up\Repository;

interface Repository
{
	public static function getAll(): array;

	public static function getById(int $id);
}