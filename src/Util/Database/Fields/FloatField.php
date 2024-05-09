<?php

namespace Up\Util\Database\Fields;

class FloatField extends DataField
{
	public function getType(): string
	{
		return 'float';
	}
}