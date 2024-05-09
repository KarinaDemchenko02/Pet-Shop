<?php

namespace Up\Util\Database\Fields;

class IntegerField extends DataField
{
	public function getType(): string
	{
		return "integer";
	}
}