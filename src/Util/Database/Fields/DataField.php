<?php

namespace Up\Util\Database\Fields;

abstract class DataField extends Field
{
	public function __construct(
		protected string $name,
		protected bool   $isPrimary = false,
		protected bool   $isNullable = true,
		protected bool   $isDefaultExists = false,
	)
	{
		parent::__construct($name);
	}
	public function isPrimary(): bool
	{
		return $this->isPrimary;
	}

	public function isNullable(): bool
	{
		return $this->isNullable;
	}

	public function isDefaultExists(): bool
	{
		return $this->isDefaultExists;
	}
}