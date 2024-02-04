<?php

namespace Up\Dto;


use Up\Entity\Entity;

interface Dto
{
	public function from(Entity $entity): void;
}
