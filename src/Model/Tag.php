<?php

namespace Up\Model;

class Tag
{

	private string $id;
	private string $title;
	private string $idParentTag;

	/**
	 * @param string $id
	 * @param string $title
	 * @param string $idParentTag
	 */
	public function __construct(string $id, string $title, string $idParentTag)
	{
		$this->id = $id;
		$this->title = $title;
		$this->idParentTag = $idParentTag;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getIdParentTag(): string
	{
		return $this->idParentTag;
	}
}
