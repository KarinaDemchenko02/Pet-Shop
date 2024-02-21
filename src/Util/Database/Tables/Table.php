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
				if ($field->conditions === $ignoredField)
				{
					continue;
				}
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
				$referenceJoins = $field->referenceTable->getJoins($field->getName());
				$conditions = $field->conditions;
				foreach ($conditions as $condition)
				{
					$condition = self::formatCondition($condition, $field->referenceTable);
					$joins[$field->referenceTable->getTableName()] = [
						'type' => $field->joinType,
						'condition' => $condition,
					];

				}
				$joins = array_merge($joins, $referenceJoins);
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

	public static function getList(array|string $selectColumns, $where = '')
	{
		$orm = Orm::getInstance();
		$rofls = self::getRofls($selectColumns);
		$columns = $rofls[0];
		$joins = $rofls[1];
		return $orm->select(static::getTableName(), $columns, joins: $joins);
	}

	private static function getJoinReference($field)
	{
		$conditions = $field->conditions;
		$joins = [];
		foreach ($conditions as $condition)
		{
			$condition = self::formatCondition($condition, $field->referenceTable);
			$joins[$field->referenceTable->getTableName()] = [
				'type' => $field->joinType,
				'condition' => $condition,
			];

		}
		return $joins;
	}

	private static function getColumns()
	{
		$selectColumns = [];
		$tableName = static::getTableName();
		foreach (static::getMap() as $field)
		{
			if ($field->getType() !== 'reference' && $field->getType() !== 'oneToMany')
			{
				$selectColumns[] = "{$field->getName()}";
			}
		}
		return $selectColumns;
	}

	private static function getRofls($selectColumns, $joins = [])
	{
		if ($selectColumns === '*')
		{
			$selectColumns = self::getColumns();
		}
		$tableName = static::getTableName();
		foreach ($selectColumns as $alias => $selectColumn)
		{
			$parts = explode(".", $selectColumn);
			$name = array_shift($parts);
			$remaining = implode(".", $parts);
			$field = self::getFieldByName($name);
			if (is_null($field))
			{
				throw new \RuntimeException("Error a column does not exist: $selectColumn");
			}
			if ($field->getType() === 'oneToMany')
			{

				continue;
			}
			if($field->getType() === 'reference')
			{
				if (empty($remaining))
				{
					$remaining = '*';
				}
				$joins = array_merge($joins, self::getJoinReference($field));
				$columns = array_merge($columns, $field->referenceTable->getRofls($remaining)[0]);
				continue;
			}
			if (is_string($alias))
			{
				$columns[] = "$tableName.$selectColumn AS $alias";
				continue;
			}
			$columns[] = "$tableName.$selectColumn";
		}
		return [$columns, $joins];
	}
	public static function update($data, $where): int
	{
		return Orm::getInstance()->update(static::getTableName(), $data, $where);
	}

	abstract public static function getMap(): array;

	abstract public static function getTableName(): string;
}