<?php

namespace Up\Repository\Tag;

use Up\Dto\Tag\TagChangingDto;
use Up\Entity\Tag;
use Up\Exceptions\Admin\Tag\TagNotChanged;
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

	public static function add(string $title): void
	{
		TagTable::add(['title' => $title]);
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

	private static function getTagList($where = []): \mysqli_result|bool
	{
		return TagTable::getList(['tag_id' => 'id', 'tag_title' => 'title'], conditions: $where);
	}
}
