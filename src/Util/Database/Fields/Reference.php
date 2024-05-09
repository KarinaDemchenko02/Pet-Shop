<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

class Reference extends Relation
{
	public function getType(): string
	{
		return 'reference';
	}

	protected function getJoinCondition(Table $thisTable): string
	{
		return $this->formatCondition($this->condition, $thisTable, $this->referenceTable);
	}
}