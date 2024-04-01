<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\Field;
use Up\Util\Database\Fields\Reference;
use Up\Util\Database\Fields\Relation;
use Up\Util\Database\Orm;

abstract class Table implements TableInterface
{
	public static function add(array $data, $isIgnore = false): int
	{
		$orm = Orm::getInstance();
		/**
		 * @var Field[] $fields
		 */
		$fields = static::getMap();
		$insertData = [];
		foreach ($fields as $field)
		{
			if (
				!isset($data[$field->getName()])
				&& !($field->getType() === 'reference'
					|| $field->getType() === 'reflection')
				&& !$field->isDefaultExists()
			)
			{
				throw new \RuntimeException("Error: too few arguments, field {$field->getName()} not found");
			}
			if (isset($data[$field->getName()]))
			{
				$insertData[$field->getName()] = $data[$field->getName()];
			}
		}
		$orm->insert(static::getTableName(), $insertData, $isIgnore);

		return $orm->affectedRows();
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

	public static function getList(
		array $selectedColumns = ['*'],
		array $conditions = [],
			  $orderBy = [],
			  $limit = null,
			  $offset = null,
	)
	{
		$orm = orm::getInstance();
		$columnJoin = self::getColumnJoin($selectedColumns);
		$where = '';
		if (!empty($conditions))
		{
			$where = self::makeWhere($conditions[0], $conditions[1], $columnJoin['alias']);
		}

		return $orm->select(
			static::getTableName(),
			$columnJoin['columns'],
			$where,
			self::makeOrderBy($orderBy, $columnJoin['alias']),
			$limit ?? '',
			$offset ?? '',
			$columnJoin['joins']
		);
	}

	private static function getColumnJoin(
		array $selectedColumns = ['*'],
			  $joins = []
	): array
	{
		$tableAlias = [];
		$tableName = static::getTableName();
		if (isset($selectedColumns[0]) && $selectedColumns[0] === '*')
		{
			array_shift($selectedColumns);
			$selectedColumns = array_merge(self::getAllColumns(), $selectedColumns);
		}
		$columns = [];

		foreach ($selectedColumns as $aliasOrTableName => $column)
		{
			$fieldName = is_array($column) ? $aliasOrTableName : $column;
			$field = self::getFieldByName($fieldName);
			if (is_null($field))
			{
				throw new \RuntimeException(
					"Error: A field with this name in the table '$tableName' was not found: '$fieldName'"
				);
			}
			$fieldType = $field->getType();
			if ($fieldType !== 'reference' && $fieldType !== 'reflection')
			{
				$fieldFullName = "$tableName.$fieldName";
				$key = array_search($fieldFullName, $columns, true);
				if (is_int($aliasOrTableName))
				{
					$tableAlias[$fieldName] = $fieldFullName;
				}
				else
				{
					$tableAlias[$aliasOrTableName] = $fieldFullName;
					if (isset($tableAlias[$fieldName]))
					{
						unset($tableAlias[$fieldName]);
					}
					$fieldFullName .= " AS $aliasOrTableName";
				}
				if ($key !== false)
				{
					$columns[$key] = $fieldFullName;
				}
				else
				{
					$columns[] = $fieldFullName;
				}

			}
			else
			{
				/* @var $field Relation */
				$selectedRelatedColumns = is_array($column) ? $column : ['*'];
				$relatedTable = $field->referenceTable;
				$relatedTableName = $relatedTable::getTableName();
				if (!isset($joins[$relatedTableName]))
				{
					if ($fieldType === 'reflection')
					{
						/* @var $relatedField Reference */
						$relatedField = $relatedTable::getFieldByName($field->condition);
						if (is_null($relatedField))
						{
							throw new \RuntimeException(
								"The name of the related field of the related table '$relatedTableName' is incorrect: {$field->condition}"
							);
						}
						$joinCondition = $relatedTable::formatCondition(
							$relatedField->condition,
							$relatedField->referenceTable
						);
					}
					else
					{
						$joinCondition = self::formatCondition($field->condition, $field->referenceTable);
					}
					$joins[$relatedTableName] = [
						'type' => $field->joinType,
						'condition' => $joinCondition,
					];
					$columnJoin = $relatedTable::getColumnJoin($selectedRelatedColumns, $joins);
					$tableAlias = array_unique(array_merge($tableAlias, $columnJoin['alias']));
					$joins = array_merge($joins, $columnJoin['joins']);
					$columns = array_unique(array_merge($columns, $columnJoin['columns']));
				}
			}
		}

		return ['columns' => $columns, 'joins' => $joins, 'alias' => $tableAlias];
	}

	private static function makeWhere($logicCondition, $conditions, $alias = []): string
	{
		$tableName = static::getTableName();
		$where = [];
		foreach ($conditions as $logic => $condition)
		{
			if ($logic === 'OR' || $logic === 'AND')
			{
				$nestedWhere = self::makeWhere($logic, $condition, $alias);
				$where[] = "($nestedWhere)";
			}
			else
			{

				preg_match('/(!)?(>=|<=|!=|=|>|<|in=|%=)(.*)/', $logic, $matches);
				$not = $matches[1] === '!' ? 'NOT ' : '';
				$func = $matches[2];
				$fieldName = $matches[3];
				$fieldName = $alias[$fieldName] ?? "$tableName.$fieldName";
				if (is_null($func))
				{
					throw new \RuntimeException("Error! Function is not set for: $fieldName");
				}
				$preparedCondition = $condition;
				if (is_string($condition))
				{
					if ($func === '%=')
					{
						$preparedCondition = "%$preparedCondition%";
					}
					$preparedCondition = self::prepareString($preparedCondition);
				}
				if (is_array($condition))
				{
					if (empty($condition))
					{
						continue;
					}
					$preparedCondition = implode(
						', ',
						$condition
					);
				}
				switch ($func)
				{
					case 'in=':
						$where[] = "$fieldName {$not}IN ($preparedCondition)";
						break;
					case '%=':
						$where[] = "$fieldName {$not}LIKE $preparedCondition";
						break;
					case '=':
					case '>':
					case '<':
					case '>=':
					case '<=':
						$where[] = "{$not}{$fieldName}{$func}{$preparedCondition}";
						break;
				}
			}
		}

		return implode(" $logicCondition ", $where);
	}

	private static function makeOrderBy($orderBy, $alias): string
	{
		$tableName = static::getTableName();
		$orderByCondition = [];
		foreach ($orderBy as $fieldName => $direction)
		{
			if ($direction !== 'DESC' && $direction !== 'ASC')
			{
				throw new \RuntimeException('Error! Sorting directions can only be DESC or ASC');
			}
			$fieldName = $alias[$fieldName] ?? "$tableName.$fieldName";
			$orderByCondition[] = "$fieldName $direction";
		}

		return implode(', ', $orderByCondition);
	}

	public static function update(array $data, array $condition): int
	{
		$orm = Orm::getInstance();
		$where = self::makeWhere($condition[0], $condition[1]);
		$orm->update(static::getTableName(), $data, $where);

		return $orm->affectedRows();
	}

	public static function delete(array $condition): int
	{
		$orm = Orm::getInstance();
		if (empty($condition))
		{
			throw new \RuntimeException("Error! Condition is cannot be empty");
		}
		$where = self::makeWhere($condition[0], $condition[1]);

		$orm->delete(static::getTableName(), $where);

		return $orm->affectedRows();
	}

	private static function prepareString($string): string
	{
		$orm = Orm::getInstance();
		if (!is_string($string))
		{
			return $string;
		}

		return $orm->escapeString($string);
	}

	private static function getAllColumns(): array
	{
		$columns = [];
		foreach (static::getMap() as $field)
		{
			if ($field->getType() !== 'reference' && $field->getType() !== 'reflection')
			{
				$columns[] = $field->getName();
			}
		}
		return $columns;
	}

	abstract public static function getMap(): array;

	abstract public static function getTableName(): string;
}