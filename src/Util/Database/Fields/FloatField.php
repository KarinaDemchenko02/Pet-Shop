<?php

namespace Up\Util\Database\Fields;

class FloatField extends Field
{
	public function getType(): string
	{
		return 'float';
	}
}