<?php

namespace Up\Repository\Tag;

use Up\Entity\Tag;
use Up\Util\Database\QueryResult;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{

		$sql = "select * from up_tags;";

		$result = QueryResult::getQueryResult($sql);

		$tags = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['id']] = new Tag($row['id'], $row['name']);
		}

		return $tags;
	}

	public static function getById(int $id): Tag
	{
		$sql = "select * from up_tags where id = {$id};";

		$result = QueryResult::getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new Tag($row['id'], $row['name']);
	}

	public static function add(string $title): bool
	{
		$sql = "INSERT INTO up_tags (name) VALUES ('{$title}');";

		$result = QueryResult::getQueryResult($sql);

		return true;
	}
}
