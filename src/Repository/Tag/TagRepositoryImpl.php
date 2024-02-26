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
		TagTable::add(['name' => $title]);
	}

	public static function getColumn(): array
	{
		$query = Query::getInstance();
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_NAME = 'up_tags'";
		$result = $query->getQueryResult($sql);
		$columns = [];
		while ($column = mysqli_fetch_column($result))
		{
			$columns[] = $column;
		}

		return $columns;
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
		TagTable::update(['name' => $dto->title], ['AND', ['id' => $dto->id]]);
		if ($orm->affectedRows() === 0)
		{
			throw new TagNotChanged();
		}
	}

	public static function createTagEntity(array $row): Tag
	{
		return new Tag($row['id_tag'], $row['name_tag']);
	}

	private static function createTagList(\mysqli_result $result): array
	{
		$tags = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['id_tag']] = self::createTagEntity($row);
		}

		return $tags;
	}

	private static function getTagList($where = []): \mysqli_result|bool
	{
		return TagTable::getList(['id_tag' => 'id', 'name_tag' => 'name'], conditions: $where);
	}
}
