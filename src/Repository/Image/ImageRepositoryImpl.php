<?php

namespace Up\Repository\Image;

use Up\Entity\Image;
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

	public static function add($path, $productId): void
	{
		ImageTable::add(['path' => $path, 'item_id' => $productId]);
	}

	public static function delete($id): void
	{
		ImageTable::delete(['AND', '=id' => $id]);
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