<?php

namespace Up\Repository\Tag;

use Up\Entity\Tag;
use Up\Repository\RepositoryImpl;

class TagRepositoryImpl extends RepositoryImpl
{

	public static function getAll(): array
	{

		$sql = "select * from up_tags;";

		$result = RepositoryImpl::getResultSQLQuery($sql);

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

		$result = RepositoryImpl::getResultSQLQuery($sql);

		$row = mysqli_fetch_assoc($result);

		return new Tag($row['id'], $row['name']);
	}

}
