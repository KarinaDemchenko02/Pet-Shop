<?php

namespace Up\Util\Database\Fields;

abstract class Field implements FieldInterface
{
	public function __construct(
		protected string $name,
	)
	{}

	public function getName(): string
	{
		return $this->name;
	}

	abstract public function getType(): string;

}