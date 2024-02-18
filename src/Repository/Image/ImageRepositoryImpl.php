<?php

namespace Up\Repository\Image;

use Up\Entity\Image;
use Up\Util\Database\Query;

class ImageRepositoryImpl implements ImageRepository
{
	private const SELECT_SQL = "select id, path, item_id from up_image ";

	public static function getById(int $id)
	{
		$query = Query::getInstance();
		$sql = self::SELECT_SQL . " where id={$id}";
		$result = $query->getQueryResult($sql);

		return self::createImageList($result)[$id];
	}

	public static function getAll()
	{
		$query = Query::getInstance();
		$result = $query->getQueryResult(self::SELECT_SQL);

		return self::createImageList($result);
	}

	private static function createImageList(\mysqli_result $result): array
	{
		$images = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$images[$row['id']] = new Image($row['id'], $row['path'], $row['item_id'], 'base');
		}

		return $images;
	}
}