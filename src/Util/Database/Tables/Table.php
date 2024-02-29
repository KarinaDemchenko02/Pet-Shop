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

		return $orm->insert(static::getTableName(), $insertData, $isIgnore);
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
	)
	{
		$tableAlias = [];
		$tableName = static::getTableName();
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
				if (is_int($aliasOrTableName))
				{
					$tableAlias[$fieldName] = $fieldFullName;
				}
				else
				{
					$tableAlias[$aliasOrTableName] = $fieldFullName;
					$fieldFullName .= " AS $aliasOrTableName";
				}
				$columns[] = $fieldFullName;
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

	public static function makeWhere($logicCondition, $conditions, $alias = []): string
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

				[$func, $fieldName] = explode('=', $logic);
				$fieldName = $alias[$fieldName] ?? "$tableName.$fieldName";
				$preparedCondition = $condition;
				if (is_string($condition))
				{
					if ($func === '%')
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
						array_map('\Up\Util\Database\Tables\Table::prepareString', $condition)
					);
				}
				$not = '';
				if (!empty($func) && $func[0] === '!')
				{
					$func = substr($func, 1);
					$not = 'NOT ';
				}
				switch ($func)
				{
					case '':
						$where[] = "{$not}$fieldName=$preparedCondition";
						break;
					case 'in':
						$where[] = "$fieldName {$not}IN ($preparedCondition)";
						break;
					case '%':
						$where[] = "$fieldName {$not}LIKE $preparedCondition";
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

	public static function update(array $data, array $condition, $isIgnore = false): int
	{
		$where = self::makeWhere($condition[0], $condition[1]);

		return Orm::getInstance()->update(static::getTableName(), $data, $where, $isIgnore);
	}

	public static function delete(array $condition): int
	{
		if (empty($condition))
		{
			throw new \RuntimeException("Error! Condition is cannot be empty");
		}
		$where = self::makeWhere($condition[0], $condition[1]);

		return Orm::getInstance()->delete(static::getTableName(), $where);
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

	abstract public static function getMap(): array;

	abstract public static function getTableName(): string;
}