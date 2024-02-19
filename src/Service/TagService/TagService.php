<?php

namespace Up\Service\TagService;


use Up\Dto\TagDto;
use Up\Repository\Tag\TagRepositoryImpl;

class TagService
{
	public static function getAllTags(): array
	{
		$tags = TagRepositoryImpl::getAll();

		$tagsDto = [];
		foreach ($tags as $tag)
		{
			$tagsDto[] = new TagDto($tag);
		}

		return $tagsDto;
	}
	public static function getTagById(int $id): TagDto
	{
		$product = TagRepositoryImpl::getById($id);
		return new TagDto($product);
	}

	public static function getColumn(): array
	{
		return TagRepositoryImpl::getColumn();
	}
}
