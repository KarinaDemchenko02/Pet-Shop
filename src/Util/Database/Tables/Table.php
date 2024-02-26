<?php

namespace Up\Util\Database\Tables;

use Up\Util\Database\Fields\Field;
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
		array $selectedRelatedColumns = [],
		array $conditions = [],
			  $orderBy = [],
			  $limit = null,
			  $offset = null,
	)
	{
		$orm = orm::getInstance();
		$columnJoin = self::getColumnJoin($selectedColumns, $selectedRelatedColumns);
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
		array $selectedRelatedColumns = [],
		array $joins = [],
		array $tableAlias = []
	)
	{
		$map = static::getMap();
		$tableName = static::getTableName();
		$columns = [];

		foreach ($map as $field)
		{
			$fieldName = $field->getName();
			$fieldType = $field->getType();

			if (
				in_array($fieldName, $selectedColumns, true)
				|| (in_array('*', $selectedColumns, true) && $fieldType !== 'reference' && $fieldType !== 'reflection')
			)
			{
				$alias = array_search($fieldName, $selectedColumns, true);
				$columnName = "$tableName.$fieldName";

				if (is_int($alias))
				{
					$tableAlias[$fieldName] = $columnName;
				}
				else
				{
					$tableAlias[$alias] = $columnName;
					$columnName .= " AS $alias";
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

					$columnJoin = $referenceTable->getColumnJoin(
						$selectedColumnsForJoin,
						$selectedRelatedColumns,
						$joins
					);
					$tableAlias = array_merge($tableAlias, $columnJoin['alias']);
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