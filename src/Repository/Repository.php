<?php

namespace Up\Repository;

abstract class Repository
{
	abstract static function getAll(): array;
	abstract static function getById(int $id);
}