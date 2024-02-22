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
		array $joins = []
	)
	{
		$map = static::getMap();
		$tableName = static::getTableName();
		$columns = [];

		$classReflection = new \ReflectionClass(static::class);
		$classShortName = $classReflection->getShortName();

		foreach ($map as $field)
		{
			$fieldName = $field->getName();
			$fieldType = $field->getType();

			if (
				in_array($fieldName, $selectedColumns, true)
				|| ($selectedColumns[0] === '*' && $fieldType !== 'reference' && $fieldType !== 'reflection')
			)
			{
				$columnName = "$tableName.$fieldName";
				$fullFieldName = "$classShortName.$fieldName";

				if (isset($columnAliases[$fullFieldName]))
				{
					$columnName .= " AS {$columnAliases[$fullFieldName]}";
				}
				$columns[] = $columnName;
			}

			if (
				($fieldType === 'reference' || $fieldType === 'reflection')
				&& (isset($selectedRelatedColumns[$fieldName])
					|| in_array(
						$fieldName,
						array_values($selectedRelatedColumns),
						true
					))
			)
			{
				$selectedColumnsForJoin = $selectedRelatedColumns[$fieldName] ?? ['*'];
				$referenceTable = $field->referenceTable;
				$referenceTableName = $referenceTable->getTableName();

				if (!isset($joins[$referenceTableName]))
				{
					if ($fieldType === 'reflection')
					{
						$referenceField = $field->referenceTable->getFieldByName($field->condition);
						$joinCondition = $field->referenceTable::formatCondition(
							$referenceField->condition,
							$referenceField->referenceTable
						);
					}
					else
					{
						$joinCondition = self::formatCondition($field->condition, $field->referenceTable);
					}

					$joins[$referenceTableName] = [
						'type' => $field->joinType,
						'condition' => $joinCondition,
					];

					$joinResult = $referenceTable->getColumnJoin(
						$selectedColumnsForJoin,
						$selectedRelatedColumns,
						$columnAliases,
						$joins
					);

					$joins = array_merge($joins, $joinResult['joins']);
					$columns = array_unique(array_merge($columns, $joinResult['columns']));
				}
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