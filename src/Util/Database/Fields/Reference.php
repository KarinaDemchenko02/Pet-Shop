<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Tables\Table;

class Reference extends Relation
{
	public function getType(): string
	{
		return 'reference';
	}
}