<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

class ReferenceField extends Field
{
	public function __construct(
		readonly Table  $referenceTable,
		readonly array  $conditions,
		readonly string $joinType = 'LEFT',
		bool            $isPrimary = false,
		bool            $isNullable = true,
		bool            $isDefaultExists = false
	)
	{
		parent::__construct($isPrimary, $isNullable, $isDefaultExists);
	}

	public function getType(): string
	{
		return 'reference';
	}
}