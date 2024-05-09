<?php

namespace Up\Util\Database\Fields;

interface FieldInterface
{
	public function getName(): string;
	public function getType(): string;

}