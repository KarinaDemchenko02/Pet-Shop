<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

abstract class Relation extends Field
{
	public function __construct(
		string          $name,
		readonly Table  $referenceTable,
		readonly string $condition,
		readonly string $joinType = 'LEFT',
	)
	{
		parent::__construct($name);
	}

	protected function formatCondition(string $condition, Table $thisTable,  Table $referenceTable): string
	{
		$condition = str_replace(['this', 'ref'],
								 [$thisTable::getTableName(), $referenceTable::getTableName()],
								 $condition);

		return $condition;

	}

	abstract protected function getJoinCondition(Table $thisTable): string;

	public function getJoin(Table $thisTable): array
	{
		return [
			'type' => $this->joinType,
			'condition' => $this->getJoinCondition($thisTable),
		];
	}
}