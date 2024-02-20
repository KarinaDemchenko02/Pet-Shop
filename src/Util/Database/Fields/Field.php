<?php

namespace Up\Util\Database\Fields;

abstract class Field implements FieldInterface
{
	public function __construct(
		protected string $name,
		protected bool   $isPrimary = false,
		protected bool   $isNullable = true,
		protected bool   $isDefaultExists = false,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	abstract public function getType(): string;

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