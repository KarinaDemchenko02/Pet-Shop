<?php

namespace Up\Repository\Tag;

use Up\Entity\Tag;
use Up\Repository\RepositoryImpl;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();

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
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();

		$sql = "select * from up_tags where id = {$id};";

		$result = RepositoryImpl::getResultSQLQuery($sql);

		$row = mysqli_fetch_assoc($result);

		return new Tag($row['id'], $row['name']);
	}

}
