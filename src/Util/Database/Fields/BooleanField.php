<?php

namespace Up\Util\Database\Fields;

use Up\Util\Database\Fields\Field;

class BooleanField extends DataField
{

	public function getType(): string
	{
		return 'bool';
	}
}