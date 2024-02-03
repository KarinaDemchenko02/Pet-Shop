<?php

namespace Up\Repository;

<<<<<<< HEAD
use Up\Entity\Entity;

interface Repository
{
	public static function getAll(): array;
	public static function getById(int $id): Entity;
}
=======
interface Repository
{
	public static function getAll(): array;

	public static function getById(int $id);
}
>>>>>>> f3757b0 (added interfaces to the repository)
