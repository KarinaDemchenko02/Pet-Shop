<?php

namespace Up\Util\Database\Fields;

class IntegerField extends Field
{
	public function getType(): string
	{
		return "integer";
	}
}