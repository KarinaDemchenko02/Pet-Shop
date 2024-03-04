<?php

namespace Up\Entity;

use Up\Entity\Entity;

class SpecialOffer implements Entity
{
	readonly string $id;
	readonly string $title;
	readonly string $description;
	readonly string $startDate;
	readonly string $endDate;

	/**
	 * @param string $id
	 * @param string $title
	 * @param string $description
	 * @param int $startDate
	 * @param int $endDate
	 */
	public function __construct(string $id, string $title, string $description, string $startDate, string $endDate)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
	}

}