<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\Field;
use Up\Util\Database\Orm;

abstract class Table implements TableInterface
{
	public static function add(array $data): int
	{
		$orm = Orm::getInstance();
		/**
		 * @var Field[] $fields
		 */
		$fields = static::getMap();
		$insertData = [];
		foreach ($fields as $field)
		{
			if (!isset($data[$field->getName()]) && !$field->isDefaultExists())
			{
				throw new \RuntimeException('Error: too few arguments');
			}
			if (isset($data[$field->getName()]))
			{
				$insertData[$field->getName()] = $data[$field->getName()];
			}
		}

		return $orm->insert(static::getTableName(), $insertData);
	}

	public static function getAll(): \mysqli_result
	{
		$fields = static::getMap();
		$joins = [];
		foreach ($fields as $field)
		{
			if ($field->getType() === 'reference')
			{
				$conditions = $field->conditions;
				foreach ($conditions as $condition)
				{
					$condition = self::formatCondition($condition, $field->referenceTable);
					$joins[$field->referenceTable->getTableName()] = [
						'type' => $field->joinType,
						'condition' => $condition,
					];
				}
			}
		}

		return Orm::getInstance()->select(static::getTableName(), static::getColumns(), joins: $joins);
	}

	/*foreach ($joins as $join => $joinData)
	{
	$joinType = $joinData['type'];
	$condition = $joinData['condition'];
	$query .= " $joinType JOIN $join ON $condition";

	'up_image' => ['type' => 'INNER', 'condition' => 'up_item.id=item_id'],
	}*/

	public static function getColumns(): array
	{
		/**
		 * @var Field[] $fields
		 */
		$fields = static::getMap();
		$fieldsNames = [];
		$tableName = static::getTableName();
		foreach ($fields as $field)
		{
			if ($field->getType() === 'reference')
			{
				$referenceFieldNames = $field->referenceTable->getColumns();
				$fieldsNames = array_merge($fieldsNames, $referenceFieldNames);
				continue;
			}
			$fieldName = $field->getName();
			$fieldsNames[] = "$tableName.$fieldName";
		}

		return $fieldsNames;
	}

	private static function formatCondition(string $condition, Table $referenceTable): string
	{
		$condition = str_replace(['this', 'ref'],
								 [static::getTableName(), $referenceTable::getTableName()],
								 $condition);

		return $condition;

	}

	abstract public static function getMap(): array;

	abstract public static function getTableName(): string;
}