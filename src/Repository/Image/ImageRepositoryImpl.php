<?php

namespace Up\Repository\Image;

use Up\Entity\Image;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\ImageTable;

class ImageRepositoryImpl implements ImageRepository
{
	public static function getById(int $id)
	{
		return self::createImageList(self::getImageList())[$id];
	}

	public static function getAll(): array
	{
		return self::createImageList(self::getImageList());
	}

	public static function delete($id)
	{
		$query = Query::getInstance();
		$deleteImageSQL = "DELETE FROM up_image WHERE id={$id}";
		$query->getQueryResult($deleteImageSQL);
	}

	private static function createImageList(\mysqli_result $result): array
	{
		$images = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$images[$row['id']] = self::createImageEntity($row);
		}

		return $images;
	}

	public static function createImageEntity($row): Image
	{
		return new Image($row['image_id'], $row['path'], $row['item_id'], 'base');
	}

	private static function getImageList($where = []): \mysqli_result|bool
	{
		return ImageTable::getList(['image_id' => 'id', 'path', 'item_id'], conditions: $where);
	}
}