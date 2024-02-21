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
		$joins = static::getJoins();

		return Orm::getInstance()->select(static::getTableName(), static::getAllColumns(), joins: $joins);
	}

	/*foreach ($joins as $join => $joinData)
	{
	$joinType = $joinData['type'];
	$condition = $joinData['condition'];
	$query .= " $joinType JOIN $join ON $condition";

	'up_image' => ['type' => 'INNER', 'condition' => 'up_item.id=item_id'],
	}*/

	public static function getAllColumns($ignoredField = null): array
	{
		/**
		 * @var Field[] $fields
		 */
		$fields = static::getMap();
		$fieldsNames = [];
		$tableName = static::getTableName();
		foreach ($fields as $field)
		{
			if ($field->getName() === $ignoredField)
			{
				continue;
			}
			if ($field->getType() === 'reference')
			{
				$referenceFieldNames = $field->referenceTable->getAllColumns($field->getName());
				$fieldsNames = array_merge($fieldsNames, $referenceFieldNames);
				continue;
			}
			if ($field->getType() === 'oneToMany')
			{
				if ($field->conditions === $ignoredField)
				{
					continue;
				}
				$referenceFieldNames = $field->referenceTable->getAllColumns($field->conditions);
				$fieldsNames = array_merge($fieldsNames, $referenceFieldNames);
				continue;
			}
			$fieldName = $field->getName();
			$fieldsNames[] = "$tableName.$fieldName AS $tableName" . '_' . (string)$fieldName;
		}

		return $fieldsNames;
	}

	public static function getJoins($ignoredField = null): array
	{
		/**
		 * @var Field[] $fields
		 */
		$fields = static::getMap();
		$joins = [];
		foreach ($fields as $field)
		{
			if ($field->getName() === $ignoredField)
			{
				continue;
			}
			if ($field->getType() === 'oneToMany')
			{
				$referenceField = $field->referenceTable->getFieldByName($field->conditions);
				$referenceJoins = $field->referenceTable->getJoins($field->conditions);

				$conditions = $referenceField->conditions;
				foreach ($conditions as $condition)
				{
					$condition = $field->referenceTable::formatCondition($condition, $referenceField->referenceTable);
					$joins[$field->referenceTable->getTableName()] = [
						'type' => $field->joinType,
						'condition' => $condition,
					];
				}
				$joins = array_merge($joins, $referenceJoins);
			}
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

		return $joins;
	}

	private static function formatCondition(string $condition, Table $referenceTable): string
	{
		$condition = str_replace(['this', 'ref'],
								 [static::getTableName(), $referenceTable::getTableName()],
								 $condition);

		return $condition;

	}

	private static function getFieldByName(string $name): ?Field
	{
		$fields = static::getMap();
		foreach ($fields as $field)
		{
			if ($field->getName() === $name)
			{
				return $field;
			}
		}

		return null;
	}

	abstract public static function getMap(): array;

	abstract public static function getTableName(): string;
}