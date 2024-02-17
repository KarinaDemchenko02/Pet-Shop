<?php

namespace Up\Repository\Tag;

use Up\Entity\Tag;
use Up\Util\Database\Query;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{
		$query = Query::getInstance();
		$sql = "select * from up_tags;";

		$result = $query->getQueryResult($sql);

		$tags = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['id']] = new Tag($row['id'], $row['name']);
		}

		return $tags;
	}

	public static function getById(int $id): Tag
	{
		$query = Query::getInstance();
		$sql = "select * from up_tags where id = {$id};";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new Tag($row['id'], $row['name']);
	}

	public static function add(string $title): bool
	{
		$query = Query::getInstance();
		$sql = "INSERT INTO up_tags (name) VALUES ('{$title}');";

		return $query->getQueryResult($sql);
	}
}
