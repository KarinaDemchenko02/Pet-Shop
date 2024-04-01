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
}