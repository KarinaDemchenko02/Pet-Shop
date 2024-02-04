<?php

namespace Up\Repository\TagRepository;

use Up\Entity;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Database\Connector::getInstance()->option('DB_HOST'),
			\Up\Util\Database\Connector::getInstance()->option('DB_USER'),
			\Up\Util\Database\Connector::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Database\Connector::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select * from up_tags;";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$tags = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['id']] = new Entity\Tag($row['id'], $row['name']);
		}

		return $tags;
	}

	public static function getById(int $id): Entity\Tag
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Database\Connector::getInstance()->option('DB_HOST'),
			\Up\Util\Database\Connector::getInstance()->option('DB_USER'),
			\Up\Util\Database\Connector::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Database\Connector::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select * from up_tags where id = {$id};";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$row = mysqli_fetch_assoc($result);

		return new Entity\Tag($row['id'], $row['name']);
	}

}
