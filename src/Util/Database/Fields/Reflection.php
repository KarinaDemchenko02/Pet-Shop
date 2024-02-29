<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

class Reflection extends Relation
{
	public function getType(): string
	{
		return 'reflection';
	}

}