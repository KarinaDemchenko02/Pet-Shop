<?php

namespace Up\Util\Database\Fields;

interface FieldInterface
{
	public function getName(): string;
	public function getType(): string;
	public function isPrimary(): bool;
	public function isNullable(): bool;
	public function isDefaultExists(): bool;

}