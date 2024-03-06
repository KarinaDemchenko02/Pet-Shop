<?php

namespace Up\Repository\Tag;

use Up\Dto\Tag\TagChangingDto;
use Up\Entity\Tag;
use Up\Exceptions\Admin\Tag\TagNotChanged;
use Up\Exceptions\Tag\TagNotAdding;
use Up\Util\Database\Orm;
use Up\Util\Database\Query;
use Up\Util\Database\Tables\TagTable;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{
		return self::createTagList(self::getTagList());
	}

	public static function getById(int $id): Tag
	{
		return self::createTagList(self::getTagList(['AND', ['=tag_id' => $id]]))[$id];
	}

	/**
	 * @throws TagNotAdding
	 */

	public static function add(string $title): string | int
	{
		$orm = Orm::getInstance();
		try
		{
			TagTable::add(['title' => $title]);

			if ($orm->affectedRows() === 0)
			{
				throw new TagNotAdding();
			}

			return $orm->last();
		}
		catch (\Throwable)
		{
			throw new TagNotAdding();
		}

	}

	public static function delete(int $id): void
	{
		TagTable::delete(['AND', ['id' => $id]]);
	}

	/**
	 * @throws TagNotChanged
	 */
	public static function change(TagChangingDto $dto): void
	{
		$orm = Orm::getInstance();
		TagTable::update(['title' => $dto->title], ['AND', ['id' => $dto->id]]);
		if ($orm->affectedRows() === 0)
		{
			throw new TagNotChanged();
		}
	}

	public static function createTagEntity(array $row): Tag
	{
		return new Tag($row['tag_id'], $row['tag_title']);
	}

	private static function createTagList(\mysqli_result $result): array
	{
		$tags = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['tag_id']] = self::createTagEntity($row);
		}

		return $tags;
	}

	public static function getAllForAdmin(int $page = 1): array
	{
		$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');
		$offset = $limit * ($page - 1);

		$result = TagTable::getList(['id'],
			limit:                      $limit,
			offset:                     $offset);
		$ids = self::getIds($result);
		if (empty($ids))
		{
			return [];
		}

		return self::createTagList(self::getTagList(['AND', ['in=id' => $ids]]));
	}

	private static function getTagList($where = []): \mysqli_result|bool
	{
		return TagTable::getList(['tag_id' => 'id', 'tag_title' => 'title'], conditions: $where);
	}

	private static function getIds(\mysqli_result $result): array
	{
		$ids = [];
		while ($row = $result->fetch_assoc())
		{
			$ids[] = $row['id'];
		}

		return $ids;
	}
}
