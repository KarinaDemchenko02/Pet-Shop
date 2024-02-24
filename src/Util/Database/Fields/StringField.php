<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Fields\Field;

class StringField extends Field
{
	public function getType(): string
	{
		return 'string';
	}
}