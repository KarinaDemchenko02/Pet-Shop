<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

class Reflection extends Relation
{
	public function getType(): string
	{
		return 'reflection';
	}

	protected function getJoinCondition(Table $thisTable): string
	{
		$relatedField = $this->referenceTable::getFieldByName($this->condition);
		if (is_null($relatedField))
		{
			throw new \RuntimeException(
				"The name of the related field of the related table '{$this->referenceTable::getTableName()}' is incorrect: {$this->condition}"
			);
		}
		if ($relatedField->getType() !== 'reference')
		{
			throw new \RuntimeException(
				"The type of the related field of the related table '{$this->referenceTable::getTableName()}' is incorrect: {$this->condition}"
			);
		}
		/* @var $relatedField Reference */
		return $this->formatCondition($relatedField->condition, $this->referenceTable, $thisTable);
	}

}