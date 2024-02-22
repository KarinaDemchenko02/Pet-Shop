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

	/*foreach ($joins as $join => $joinData)
	{
	$joinType = $joinData['type'];
	$condition = $joinData['condition'];
	$query .= " $joinType JOIN $join ON $condition";

	'up_image' => ['type' => 'INNER', 'condition' => 'up_item.id=item_id'],
	}*/

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

	public static function getColumnJoin(
		array $selectedColumns = ['*'],
		array $selectedRelatedColumns = [],
		array $columnAliases = [],
		array $joins = [],
	)
	{
		$map = static::getMap();
		$tableName = static::getTableName();
		$columns = [];
		foreach ($map as $field)
		{
			if (
				$field->getType() === 'reference'
				&& (array_key_exists($field->getName(), $selectedRelatedColumns)
					|| in_array(
						$field->getName(),
						array_values($selectedRelatedColumns),
						true
					))
			)
			{
				$selectColumns = ['*'];
				if (isset($selectedRelatedColumns[$field->getName()]))
				{
					$selectColumns = $selectedRelatedColumns[$field->getName()];
				}
				if (isset($joins[$field->referenceTable->getTableName()]))
				{
					continue;
				}
				$condition = self::formatCondition($field->condition, $field->referenceTable);
				$joins[$field->referenceTable->getTableName()] = [
					'type' => $field->joinType,
					'condition' => $condition,
				];
				$ahaha = $field->referenceTable->getColumnJoin(
					$selectColumns,
					$selectedRelatedColumns,
					$columnAliases,
					$joins
				);
				$joins = array_merge($joins, $ahaha['joins']);
				$columns = array_unique(array_merge($columns, $ahaha['columns']));
			}
			elseif (
				$field->getType() === 'oneToMany'
				&& (array_key_exists($field->getName(), $selectedRelatedColumns)
					|| in_array(
						$field->getName(),
						array_values($selectedRelatedColumns),
						true
					))
			)
			{
				$selectColumns = ['*'];
				if (isset($selectedRelatedColumns[$field->getName()]))
				{
					$selectColumns = $selectedRelatedColumns[$field->getName()];
				}
				if (isset($joins[$field->referenceTable->getTableName()]))
				{
					continue;
				}
				$referenceField = $field->referenceTable->getFieldByName($field->conditions);
				$condition = $field->referenceTable::formatCondition($referenceField->condition, $referenceField->referenceTable);
				$joins[$field->referenceTable->getTableName()] = [
					'type' => $field->joinType,
					'condition' => $condition,
				];
				$ahaha = $field->referenceTable->getColumnJoin($selectColumns, $selectedRelatedColumns, $columnAliases, $joins);
				$joins = array_merge($joins, $ahaha['joins']);
				$columns = array_unique(array_merge($columns, $ahaha['columns']));

			}
			elseif (
				$field->getType() === 'manyToMany'
				&& (array_key_exists($field->getName(), $selectedRelatedColumns)
					|| in_array(
						$field->getName(),
						array_values($selectedRelatedColumns),
						true
					))
			)
			{
				$selectColumns = ['*'];
				if (isset($selectedRelatedColumns[$field->getName()]))
				{
					$selectColumns = $selectedRelatedColumns[$field->getName()];
				}
				if (isset($joins[$field->referenceTable->getTableName()]))
				{
					continue;
				}
				$referenceField = $field->referenceTable->getFieldByName($field->conditions);
				$condition = $field->referenceTable::formatCondition($referenceField->condition, $referenceField->referenceTable);
				$joins[$field->referenceTable->getTableName()] = [
					'type' => $field->joinType,
					'condition' => $condition,
				];
				$ahaha = $field->referenceTable->getColumnJoin($selectColumns, $selectedRelatedColumns, $columnAliases, $joins);
				$joins = array_merge($joins, $ahaha['joins']);
				$columns = array_unique(array_merge($columns, $ahaha['columns']));
			}
			if (
				(in_array($field->getName(), $selectedColumns, true) || $selectedColumns[0] === '*')
				&& ($field->getType() !== 'reference' && $field->getType() !== 'oneToMany'
					&& $field->getType() !== 'manyToMany')
			)
			{
				$columnsName = "$tableName.{$field->getName()}";
				$rofls = new \ReflectionClass(static::class);
				$something = $rofls->getShortName() . '.' . $field->getName();
				if (isset($columnAliases[$something]))
				{
					$columnsName .= " AS $columnAliases[$something]";
				}
				$columns[] = $columnsName;
			}
		}

		return ['columns' => $columns, 'joins' => $joins];
	}

	public static function update($data, $where): int
	{
		return Orm::getInstance()->update(static::getTableName(), $data, $where);
	}

	public function getMapWithoutReferences($columns = ['*'])
	{
		$fields = self::getMap();
		$map = [];
		foreach ($fields as $field)
		{
			if (
				$field->getType() === 'reference' || $field->getType() === 'oneToMany'
				|| $field->getType() === 'manyToMany'
			)
			{
				continue;
			}
			if (in_array($field->getName(), $columns, true) || $columns[0] === '*')
			{
				$map[] = $field;
			}
		}

		return $map;
	}

	abstract public static function getMap(): array;

	abstract public static function getTableName(): string;
}