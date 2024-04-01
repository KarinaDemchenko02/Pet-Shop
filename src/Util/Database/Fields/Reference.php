<?php

namespace Up\Util\Database\Fields;

class Reference extends Relation
{
	public function getType(): string
	{
		return 'reference';
	}
}