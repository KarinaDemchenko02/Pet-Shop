<?php

namespace Up\Repository\Tag;

use Up\Models;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{
		$connection = \Up\Service\Database::getInstance(
			\Up\Service\Configuration::getInstance()->option('DB_HOST'),
			\Up\Service\Configuration::getInstance()->option('DB_USER'),
			\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Service\Configuration::getInstance()->option('DB_NAME')
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
			$tags[$row['id']] = new Models\Tag($row['id'], $row['name']);
		}

		return $tags;
	}

	public static function getById(int $id): Models\Tag
	{
		$connection = \Up\Service\Database::getInstance(
			\Up\Service\Configuration::getInstance()->option('DB_HOST'),
			\Up\Service\Configuration::getInstance()->option('DB_USER'),
			\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Service\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$sql = "select * from up_tags where id = {$id};";

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new Exception(mysqli_error($connection));
		}

		$row = mysqli_fetch_assoc($result);

		return new Models\Tag($row['id'], $row['name']);
	}

}