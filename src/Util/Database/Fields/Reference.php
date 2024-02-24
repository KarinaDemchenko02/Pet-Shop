<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

class Reference extends Field
{
	public function __construct(
		string $name,
		readonly Table  $referenceTable,
		readonly string  $condition,
		readonly string $joinType = 'LEFT',
		bool            $isPrimary = false,
		bool            $isNullable = true,
		bool            $isDefaultExists = false
	)
	{
		parent::__construct($name, $isPrimary, $isNullable, $isDefaultExists);
	}

	public function getType(): string
	{
		return 'reference';
	}
}